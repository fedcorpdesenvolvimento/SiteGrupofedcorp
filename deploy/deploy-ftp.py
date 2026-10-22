#!/usr/bin/env python3
"""
Envia arquivos do site local para a produção (R4C) via FTPS.

Uso (PowerShell, dentro de app/public):
  $env:FTP_PW = "senha-do-ftp"
  python deploy/deploy-ftp.py wp-content/mu-plugins            # envia uma pasta/arquivo
  python deploy/deploy-ftp.py --since v1.0                      # envia o que mudou no git desde a tag/commit
  python deploy/deploy-ftp.py --since HEAD~1 --dry-run          # só lista, não envia

Nunca envia: wp-config.php, .htaccess, núcleo do WordPress, caches e backups.
"""
import os, ssl, sys, subprocess
from ftplib import FTP_TLS

HOST, PORT, USER = "177.87.83.39", 21, "gfcorpalberto"
REMOTE_ROOT = "web"          # pasta do WordPress no servidor
BLOQUEADOS = ("wp-config.php", ".htaccess", "wp-admin/", "wp-includes/", "duplicator-backups/",
              "uploads/elementor/css/", "unlimited_elements_cache/", ".git", "deploy/")

def conectar():
    pw = os.environ.get("FTP_PW")
    if not pw:
        sys.exit("Defina a variável de ambiente FTP_PW com a senha do FTP.")
    ctx = ssl.create_default_context(); ctx.check_hostname = False; ctx.verify_mode = ssl.CERT_NONE
    f = FTP_TLS(context=ctx, timeout=60); f.connect(HOST, PORT); f.auth(); f.login(USER, pw); f.prot_p()
    return f

def arquivos_de(caminho):
    if os.path.isfile(caminho):
        yield caminho.replace(os.sep, "/")
    for root, _, files in os.walk(caminho):
        for fn in files:
            yield os.path.join(root, fn).replace(os.sep, "/")

def permitido(rel):
    return not any(b in rel for b in BLOQUEADOS)

def mkdirs(f, remoto):
    partes = remoto.split("/"); atual = ""
    for p in partes:
        atual = f"{atual}/{p}" if atual else p
        try: f.mkd(atual)
        except Exception: pass

def main():
    args = sys.argv[1:]; dry = "--dry-run" in args; args = [a for a in args if a != "--dry-run"]
    if args[:1] == ["--since"]:
        ref = args[1]
        saida = subprocess.check_output(["git", "diff", "--name-only", "--diff-filter=ACMR", ref, "HEAD"], text=True)
        lista = [l.strip() for l in saida.splitlines() if l.strip()]
    else:
        lista = [p for a in args for p in arquivos_de(a)]
    lista = [p for p in lista if permitido(p) and os.path.isfile(p)]
    if not lista:
        sys.exit("Nada para enviar.")
    print(f"{len(lista)} arquivo(s):"); [print("  ", p) for p in lista]
    if dry: return
    f = conectar()
    for rel in lista:
        remoto = f"{REMOTE_ROOT}/{rel}"
        mkdirs(f, remoto.rsplit("/", 1)[0])
        with open(rel, "rb") as fh:
            f.storbinary("STOR " + remoto, fh)
        print("OK", remoto)
    f.quit(); print("Concluído. Limpe o cache do site e confira em aba anônima.")

if __name__ == "__main__":
    main()
