#!/usr/bin/env python3

import os
import sys
import subprocess
import zipfile
from datetime import datetime

LOG_PATH = "/opt/vpn-cert-generator/logs/exec.log"
STORAGE_DIR = "/var/www/html/storage"

def log_event(message: str):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_PATH, "a") as log_file:
        log_file.write(f"{timestamp} - {message}\n")

def executar_comando(cmd: list):
    subprocess.run(cmd, check=True)

def criar_certificados(username: str, key_path: str, crt_path: str):
    executar_comando(["openssl", "genrsa", "-out", key_path, "2048"])
    executar_comando([
        "openssl", "req", "-new", "-x509",
        "-key", key_path, "-out", crt_path,
        "-days", "7", "-subj", f"/CN={username}"
    ])

def combinar_certificados_em_pem(key_path: str, crt_path: str, pem_path: str):
    with open(pem_path, "w") as pem_file:
        with open(key_path, "r") as key_file:
            pem_file.write(key_file.read())
        with open(crt_path, "r") as crt_file:
            pem_file.write(crt_file.read())

def criar_zip_sem_senha(zip_path: str, arquivos: list):
    with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as zipf:
        for arquivo in arquivos:
            zipf.write(arquivo, arcname=os.path.basename(arquivo))

def criar_zip_com_senha(zip_path: str, senha: str, arquivos: list):
    cmd = ["zip", "-P", senha, os.path.basename(zip_path)] + [os.path.basename(a) for a in arquivos]
    executar_comando(cmd + ["-q"])
    log_event(f"Certificado protegido por senha gerado: {zip_path}")

def limpar_arquivos(paths: list):
    for path in paths:
        if os.path.exists(path):
            os.remove(path)

def gerar_certificado(username: str, senha_zip: str = None):
    os.makedirs(STORAGE_DIR, exist_ok=True)

    key = os.path.join(STORAGE_DIR, f"{username}.key")
    crt = os.path.join(STORAGE_DIR, f"{username}.crt")
    pem = os.path.join(STORAGE_DIR, f"{username}.pem")
    zip_padrao = os.path.join(STORAGE_DIR, f"{username}_cert.zip")
    zip_protegido = os.path.join(STORAGE_DIR, f"{username}_cert_protected.zip")

    try:
        criar_certificados(username, key, crt)
        combinar_certificados_em_pem(key, crt, pem)

        if senha_zip:
            os.chdir(STORAGE_DIR)
            criar_zip_com_senha(zip_protegido, senha_zip, [key, crt, pem])
            print(f"Arquivo protegido gerado: {zip_protegido}")
        else:
            criar_zip_sem_senha(zip_padrao, [key, crt, pem])
            log_event(f"Certificado sem senha gerado: {zip_padrao}")
            print(f"Arquivo gerado: {zip_padrao}")

    except subprocess.CalledProcessError as e:
        log_event(f"Erro ao gerar certificado para '{username}': {e}")
        print("Erro ao gerar certificado. Veja logs.")
    finally:
        limpar_arquivos([key, crt, pem])

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Uso: sudo python3 gerar_certificado.py <username> [senha_zip]")
        sys.exit(1)

    user = sys.argv[1]
    password = sys.argv[2] if len(sys.argv) == 3 else None
    gerar_certificado(user, password)
