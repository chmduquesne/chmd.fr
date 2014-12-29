all: clean index.html gpg.html

%.html: %.md style.css
	pandoc -s -c style.css -H header-links -o $@ $<

clean:
	rm -f *.html

