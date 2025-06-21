#!/usr/bin/env python3

import os
import json
import sys
from datetime import datetime

LOG_FILE = "/opt/vpn-cert-generator/logs/exec.log"
STORAGE_DIR = "/var/www/html/storage"
REGISTROS_JSON = os.path.join(STORAGE_DIR, "registros.json")

def log_event(message: str):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_FILE, "a") as log_file:
        log_file.write(f"{timestamp} - {message}\n")

def remover_arquivo(caminho: str) -> bool:
    if os.path.isfile(caminho):
        os.remove(caminho)
        return True
    return False

def remover_registro(username: str):
    if not os.path.isfile(REGISTROS_JSON):
        log_event("Arquivo registros.json não encontrado.")
        return

    with open(REGISTROS_JSON, "r") as f:
        try:
            registros = json.load(f)
        except json.JSONDecodeError:
            log_event("Erro ao ler registros.json: JSON inválido.")
            return

    novos_registros = [r for r in registros if r.get("id") != username]

    with open(REGISTROS_JSON, "w") as f:
        json.dump(novos_registros, f, indent=4)

    log_event(f"Registro '{username}' removido do JSON.")

def excluir_certificado(username: str):
    zip_file = os.path.join(STORAGE_DIR, f"{username}_cert.zip")

    try:
        if remover_arquivo(zip_file):
            log_event(f"Certificado '{username}' removido do storage.")
        else:
            log_event(f"Arquivo '{zip_file}' não encontrado.")

        remover_registro(username)

    except Exception as e:
        log_event(f"Erro ao excluir certificado '{username}': {e}")

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Uso: sudo python3 deletar_certificado.py <username>")
        sys.exit(1)

    usuario = sys.argv[1]
    excluir_certificado(usuario)
