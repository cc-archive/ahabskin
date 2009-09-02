#!/usr/bin/python
import csv
import urllib2
import urllib
import urlparse

QUERY='Special:Ask/-5B-5BCategory:Sidebox-5D-5D-20-5B-5BEnabled::True-5D-5D/sort%3DFront_page_order/order%3DASC/format%3Dcsv/sep%3D%2C/limit%3D100'
LOCALHOST_BASE_URL = 'http://localhost.localdomain/~paulproteus/ed/index.php/'


# Look at me, I'm doing HTML manipulation with string functions.

def main(BASE_URL):
    data_url = BASE_URL + QUERY
    data = urllib2.urlopen(data_url)
    reader = csv.reader(data)
    html_bits = []
    for csv_row in reader:
        if not csv_row: continue
        row, = csv_row

        # Remove spurious User: junk
        if row.startswith('User:'):
            continue
        # Otherwise, we need two forms. Either way, we need this page rendered.
        rendered_url = urlparse.urljoin(BASE_URL, urllib.quote(row))
        rendered_html = unicode(urllib2.urlopen(rendered_url + '?action=render').read(), 'utf-8')
        # Strip out the mw-headline class )-:
        rendered_html = rendered_html.replace('class="mw-headline"', '')
        html_bits.append(rendered_html)
    # Join them together, tragically lamely.
    html = []
    html.append(u'<ul id="slider">')
    for html_bit in html_bits:
        # Pick which <li> class to use
        if '<img' in html_bit:
            html.append(u'<li class="panel with_image">')
        else:
            html.append(u'<li class="panel">')
        # either way, add the bit and a slash li tag
        html.append(html_bit)
        html.append('</li>')
    html.append('</ul>')
    return '\n'.join(html)

if __name__ == '__main__':
    import sys
    if len(sys.argv) > 1:
        base = sys.argv[1]
    else:
        base = LOCALHOST_BASE_URL
    print main(base).encode('utf-8')
