#!/usr/bin/env python3
"""Check release versions, bilingual FAQ and localized German changelog prefixes."""
from pathlib import Path
import re
root = Path(__file__).resolve().parents[1]
version = re.search(r'Version:\s*(\S+)', (root/'builder-shortcode-extras.php').read_text())[1]
for name in ('README.md','README-de.md','readme.txt','readme-de.txt','docs/CHANGELOG.md','docs/CHANGELOG-de.md','docs/changelog.txt','docs/changelog-de.txt','docs/English.md','docs/Deutsch.md'):
    assert version in (root/name).read_text(), f'Version mismatch: {name}'
for name in ('README-de.md','readme-de.txt','docs/CHANGELOG-de.md','docs/changelog-de.txt','docs/wiki/CHANGELOG-de.md'):
    text = (root/name).read_text()
    assert not re.search(r'\b(New|Improved|Fixed|Misc):', text), f'Untranslated prefix: {name}'
    for prefix in ('Neu:', 'Verbessert:', 'Behoben:', 'Sonstiges:'):
        assert prefix in text, f'Missing prefix {prefix}: {name}'
for en, de in (('README.md','README-de.md'), ('docs/English.md','docs/Deutsch.md'), ('docs/wiki/English.md','docs/wiki/Deutsch.md')):
    sections=[]
    for name in (en,de):
        text=(root/name).read_text()
        marker='## FAQ' if name.startswith('README') or name.endswith('English.md') else '## Fragen nach Themen'
        sections.append(text.split(marker,1)[1].split('\n## ',1)[0])
    pattern=r'^### ' if en.startswith('README') else r'^#### '
    counts=[len(re.findall(pattern,text,re.M)) for text in sections]
    assert counts[0] > 0 and counts[0] == counts[1], f'FAQ mismatch: {en}, {de}'
    if en.startswith('README'):assert counts[0] <= 7, 'Readme FAQ exceeds seven'
for name in ('readme.txt','readme-de.txt'):
    faq=(root/name).read_text().split('== Frequently Asked Questions ==',1)[1].split('== Changelog ==',1)[0]
    assert 0 < len(re.findall(r'^= .+ =$',faq,re.M)) <= 7, f'FAQ limit: {name}'
for name in ('English.md','Deutsch.md','CHANGELOG.md','CHANGELOG-de.md'):
    normalize = lambda text: re.sub(r'\]\([^)]*\)', ']()', text)
    assert normalize((root/'docs'/name).read_text()) == normalize((root/'docs/wiki'/name).read_text()), f'Wiki mismatch: {name}'
print(f'PASS release {version}: versions, German prefixes, bilingual FAQ, synchronized wiki sources')
