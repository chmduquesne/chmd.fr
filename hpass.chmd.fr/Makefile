all: index.html

%.html: %.md style.css
	pandoc -s -c //chmd.fr/style.css -H header -o $@ $<

clean:
	rm -f *.html

