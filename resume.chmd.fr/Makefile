MD=$(wildcard *.md)

HTML=$(MD:.md=.html)
PDF=$(MD:.md=.pdf)
DOCX=$(MD:.md=.docx)
TXT=$(MD:.md=.txt)

all: $(HTML)

%.html: %.md style.css
	pandoc -s -c style.css -o $@ $<

%.pdf: %.html
	wkhtmltopdf $< $@

%.docx: %.md
	pandoc -o $@ $<

%.txt: %.md
	pandoc -s -S -t plain -o $@ $<

clean:
	rm -f *.html *.pdf *.docx *.txt
