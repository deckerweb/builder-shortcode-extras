#!/usr/bin/env python3
"""Refresh the host catalogs and merge the embedded component messages."""
from pathlib import Path
import ast,json,re,subprocess
r=Path(__file__).resolve().parents[1]
def parse(path):
 entries=[];entry={};current=None
 for line in path.read_text().splitlines()+['']:
  if not line.strip():
   if 'msgid' in entry:entries.append(entry)
   entry={};current=None;continue
  match=re.match(r'(msgctxt|msgid_plural|msgid|msgstr(?:\[\d+\])?)\s+(".*")$',line)
  if match:current=match[1];entry[current]=ast.literal_eval(match[2])
  elif line.startswith('"') and current:entry[current]+=ast.literal_eval(line)
 return entries
def key(e):return e.get('msgctxt','')+'\x04'+e['msgid']
def quote(s):return json.dumps(s,ensure_ascii=False)
files=[r/'builder-shortcode-extras.php',r/'uninstall.php']+[p for p in (r/'includes').rglob('*.php') if 'deckerweb-plugin-library' not in str(p) and p.name!='deckerweb-github-release-updater-v2.php']
subprocess.run(['xgettext','--language=PHP','--from-code=UTF-8','--keyword=__','--keyword=_e','--keyword=_x:1,2c','--keyword=_n:1,2','--keyword=_nx:1,2,4c','--keyword=_n_noop:1,2','--keyword=_nx_noop:1,2,3c','--keyword=esc_html__','--keyword=esc_attr__','--keyword=esc_html_e','--keyword=esc_attr_e','--keyword=esc_html_x:1,2c','--keyword=esc_attr_x:1,2c','--package-name=Builder Shortcode Extras','--package-version=1.2.0','-o',str(r/'languages/builder-shortcode-extras.pot')]+[str(p) for p in files],check=True)
pot=r/'languages/builder-shortcode-extras.pot';entries=parse(pot);seen={key(e) for e in entries}
lib=r/'includes/deckerweb-plugin-library';messages=json.loads((lib/'messages.json').read_text())
for message in messages:
 e={'msgid':message,'msgstr':''}
 if key(e) not in seen:entries.append(e);seen.add(key(e))
updater=json.loads((r/'tools/updater-translations.json').read_text())
extra={
 'Documentation':{'de_DE':'Dokumentation','de_DE_formal':'Dokumentation'},
 'Settings':{'de_DE':'Einstellungen','de_DE_formal':'Einstellungen'},
 'Newsletter':{'de_DE':'Newsletter','de_DE_formal':'Newsletter'},
 'Builder Shortcode Extras cannot use the shared updater loaded by another plugin. Update the other deckerweb plugins or install the latest plugin ZIP manually.':{'de_DE':'Builder Shortcode Extras kann den von einem anderen Plugin geladenen gemeinsamen Updater nicht verwenden. Aktualisiere die anderen deckerweb Plugins oder installiere das aktuelle Plugin-ZIP manuell.','de_DE_formal':'Builder Shortcode Extras kann den von einem anderen Plugin geladenen gemeinsamen Updater nicht verwenden. Aktualisieren Sie die anderen deckerweb Plugins oder installieren Sie das aktuelle Plugin-ZIP manuell.'},
}
def serialize(entries):
 blocks=[]
 for e in entries:
  order=[k for k in ['msgctxt','msgid','msgid_plural','msgstr','msgstr[0]','msgstr[1]'] if k in e]
  blocks.append('\n'.join(k+' '+quote(e[k]) for k in order))
 return '\n\n'.join(blocks)+'\n'
header=entries[0];header['msgstr']=header['msgstr'].replace('CHARSET','UTF-8');pot.write_text(serialize(entries))
for locale in ['de_DE','de_DE_formal']:
 path=r/'languages'/f'builder-shortcode-extras-{locale}.po';old={key(e):e for e in parse(path)};out=[]
 for entry in entries:
  e=dict(entry);previous=old.get(key(entry))
  if previous:
   for k,v in previous.items():
    if k.startswith('msgstr'):e[k]=v
  if not e['msgid']:
   e['msgstr']=old[key(e)]['msgstr'];out.append(e);continue
  if 'msgid_plural' in e:
   e.pop('msgstr',None)
   if not all(e.get(f'msgstr[{i}]') for i in [0,1]):raise RuntimeError('Untranslated plural: '+e['msgid'])
  else:
   options=extra.get(e['msgid']) or updater.get(e['msgid']) or messages.get(e['msgid'])
   if not e.get('msgstr') and options:e['msgstr']=options[locale]
   if not e.get('msgstr'):raise RuntimeError('Untranslated message: '+e['msgid'])
  out.append(e)
 path.write_text(serialize(out));subprocess.run(['msgfmt','--check','-o',str(path.with_suffix('.mo')),str(path)],check=True)
 print('PASS',locale,len(out)-1,'translated source messages')
