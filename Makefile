install:
	cp bin/* /usr/bin
	mkdir -p /var/lib/stt
	cp -R lib/* /var/lib/stt

uninstall:
	rm -Rf /usr/bin/stt /usr/bin/legacyStt /var/lib/stt

