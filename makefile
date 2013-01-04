phar: nosh.phar
	bin/build_phar.php
	chmod +x nosh.phar

package: phar
	pear package