#!/bin/python3

import os, subprocess, shutil, sys
from pathlib import Path

PROD = 'prod'
TEST = 'test'

VARS = {
    '$SERVER_TYPE': ['Running', 'Test'],
    '$API_PORT': ['5000', '5001'],
    '$DATABASE': ['grow', 'grow_test'],
}

DEBUG = False


def copy_static_files(dirname):
    dirname = Path('stage').joinpath(dirname)
    shutil.copytree(Path('static/html'), dirname.joinpath('html'), dirs_exist_ok=True)
    shutil.copytree(Path('static/php'), dirname.joinpath('html').joinpath('php'), dirs_exist_ok=True)
    shutil.copytree(Path('static/js'), dirname.joinpath('html').joinpath('js'), dirs_exist_ok=True)
    shutil.copytree(Path('api'), dirname.joinpath('api'), dirs_exist_ok=True)


def replace_vars(dirname):
    path = Path('stage').joinpath(dirname)
    if not path.is_dir():
        print(f'Error: Staging path {path} is not a directory')
        return

    if DEBUG:
        if dirname == PROD:
            print(f'Staging production server in directory: {path}')
        else:
            print(f'Staging testing server in directory: {path}')

    for filename in list(path.glob('**/*.html')) + list(path.glob('**/*.php')) + list(path.glob('**/*.js')) + list(path.glob('**/*.py')):
        if DEBUG:
            print(f'Staging file: {filename}')
        for ident in VARS.keys():
            if dirname == PROD:
                value = VARS[ident][0]
            else:
                value = VARS[ident][1]
            if DEBUG:
                print(f'Replacing {ident} with {value}')
            subprocess.run(['sed', '-i', f's/{ident}/{value}/g', filename])


def stage(dirname):
    stage_path = Path('stage')
    if not stage_path.is_dir():
        os.mkdir(stage_path)
    copy_static_files(dirname)
    replace_vars(dirname)
    src = stage_path.joinpath(dirname).joinpath('html')
    dst = Path('/var/www').joinpath(dirname)
    
    if DEBUG:
        print(f'Copying {src} to {dst}...')
        print('')
    shutil.copytree(src, dst, dirs_exist_ok=True)

    src = stage_path.joinpath(dirname).joinpath('api')
    dst = Path('/var/growapi').joinpath(dirname)
    if not dst.is_dir():
        os.makedirs(dst, exist_ok=True)

    if DEBUG:
        print(f'Copying {src} to {dst}...')
        print('')
    shutil.copytree(src, dst, dirs_exist_ok=True)

    src = Path('run_api.sh')
    dst = Path('/var/growapi')
    if DEBUG:
        print(f'Copying {src} to {dst}...')
        print('')

    shutil.copy(src, dst)


if __name__ == '__main__':
    if len(sys.argv) > 1 and sys.argv[1].lower() == 'debug':
        DEBUG = True
        print('List of replacable identifiers:')
        for ident in VARS.keys():
            print(ident)
        print('')
    p = Path('stage')
    if p.is_dir():
        shutil.rmtree(p)
    stage(PROD)
    stage(TEST)
