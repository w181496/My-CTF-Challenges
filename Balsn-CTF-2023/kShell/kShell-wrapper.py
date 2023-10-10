#!/usr/bin/python3 -u
import os, sys
import re
import pty
import uuid
import requests
import sqlite3
import time
from time import sleep
from tempfile import mkstemp
from subprocess import check_output

COLORS = {
    'header': '\033[95m',
    'blue': '\033[94m',
    'cyan': '\033[96m',
    'green': '\033[92m',
    'warning': '\033[93m',
    'fail': '\033[91m',
    'endc': '\033[0m',
    'bold': '\033[1m',
    'underline': '\033[4m',
    'blink': '\033[5m',
}

conn = sqlite3.connect('lock.db')
cursor = conn.cursor()

# Create a lock table if it doesn't exist
cursor.execute('''CREATE TABLE IF NOT EXISTS locks (
                    name TEXT PRIMARY KEY,
                    locked INTEGER
                )''')

def acquire_lock(lock_name):
    try:
        cursor.execute("INSERT INTO locks (name, locked) VALUES (?, 1)", (lock_name,))
        conn.commit()
        return True
    except sqlite3.IntegrityError:
        # Lock is already held by someone else
        print(_color("Error! There is another session from your teammate.", 'warning'))
        return False

def release_lock(lock_name):
    cursor.execute("DELETE FROM locks WHERE name=?", (lock_name,))
    conn.commit()

def check_token(token):
    def _is_valid_uuid(s):
        try:
            return len(s) == 69 and bool(re.match(r'^[0-9A-Fa-f_t]+$', s))
        except:
            return False

    if _is_valid_uuid(token):
        r = requests.get("https://balsnctf.com/api/v1/teams/me", headers={"Authorization": "Token {}".format(token), "Content-Type": "application/json"})
        if r.json().get('success'):
            return [r.json().get('success'), r.json().get('data')["id"]]
        else:
            return [False, -1]
    else:
        return [False, -1]

def my_exec(cmds):
    return check_output(cmds)

def _color(s, color=''):
    code = COLORS.get(color)
    if code:
        return COLORS['bold'] + code + s + COLORS['endc'] + COLORS['endc']
    else:
        return s

if __name__ == '__main__':
    token = input(_color('Your access_token: ', 'bold')).strip()
    team_info = check_token(token)
    if not team_info or not team_info[0]:
        print(_color('Bad token. Bye!\n', 'warning'))
        exit(-1)

    # lock
    if acquire_lock(team_info[1]):
        try:
            name = 'team-%s' % team_info[1]
            cmds = [
                'docker', 'ps', '-q',
                '-f', 'name=%s' % name
            ]
            container_id = my_exec(cmds)
            if container_id:
                print(_color('[*] Connecting to initialized instance...', 'bold'))
            else:
                print(_color('[*] Initializing instance...', 'bold'))

                cmds = [
                    'docker', 'rm', '-f', name
                ]
                try:
                    with open(os.devnull, 'w') as devnull:
                        check_output(cmds, stderr=devnull)
                except:
                    pass

                cmds = [
                    'docker', 'run', '-d', '--rm',
                    '--name', name,
                    'kshell'
                ]
                my_exec(cmds)
                sleep(2)

            print(_color('[*] The instance will be reset every 5 minutes.\n', 'bold'))

            cmds = [
                'docker', 'exec', '-ti',
                '-u', 'kShell',
                '-e', 'TEAM_NAME="%s"' % name,
                name,
                'python3', '/kShell.py', 'tty'
            ]

            pty.spawn(cmds)
        finally:
            release_lock(team_info[1])
    conn.close()
