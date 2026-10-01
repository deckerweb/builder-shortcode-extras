#!/usr/bin/env python3
"""Build a canonical, reproducible WordPress test/release ZIP from this checkout."""
from pathlib import Path
import argparse, zipfile, re
parser=argparse.ArgumentParser();parser.add_argument('--output',required=True);args=parser.parse_args()
root=Path(__file__).resolve().parents[1];target=Path(args.output).resolve();target.parent.mkdir(parents=True,exist_ok=True)
excluded={'.git','.github','assets-repos','tests','tools','__MACOSX'}
files=[p for p in root.rglob('*') if p.is_file() and not any(s in excluded for s in p.relative_to(root).parts) and not p.name.startswith('.') and '_old' not in p.name and p!=target and 'docs/wiki' not in p.relative_to(root).as_posix() and p.name!='_config.yml']
with zipfile.ZipFile(target,'w',zipfile.ZIP_DEFLATED,compresslevel=9) as archive:
 for p in sorted(files):
  relative=p.relative_to(root).as_posix();info=zipfile.ZipInfo('builder-shortcode-extras/'+relative,date_time=(2026,10,1,0,0,0));info.external_attr=0o100644<<16;info.compress_type=zipfile.ZIP_DEFLATED;archive.writestr(info,p.read_bytes())
with zipfile.ZipFile(target) as archive:
 if archive.testzip() is not None:raise RuntimeError('ZIP integrity failed')
 main=archive.read('builder-shortcode-extras/builder-shortcode-extras.php').decode();version=re.search(r'Version:\s*(\S+)',main)[1]
 for name in ['README.md','README-de.md','readme.txt','readme-de.txt']:
  if version not in archive.read('builder-shortcode-extras/'+name).decode():raise RuntimeError('Readme version mismatch: '+name)
print(f'{target.name}: {len(files)} files, version {version}, canonical plugin directory verified.')
