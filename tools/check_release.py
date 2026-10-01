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
    texts = [(root/name).read_text().split('## FAQ',1)[1].split('\n## ',1)[0] for name in (en,de)]
    counts = [len(re.findall(r'^### ', text, re.M)) for text in texts]
    assert counts[0] > 0 and counts[0] == counts[1], f'FAQ mismatch: {en}, {de}'
for name in ('English.md','Deutsch.md','CHANGELOG.md','CHANGELOG-de.md'):
    normalize = lambda text: re.sub(r'\]\([^)]*\)', ']()', text)
    assert normalize((root/'docs'/name).read_text()) == normalize((root/'docs/wiki'/name).read_text()), f'Wiki mismatch: {name}'
print(f'PASS release {version}: versions, German prefixes, bilingual FAQ, synchronized wiki sources')
