#!/bin/sh


#WARNING! THIS SCRIPT GENERATE A FILE IN THE CURRENT DIR!
#Just kidding... not true anymore

#use of the commandline 'hostname'. Is it dangerous?

#CAplPATH=/usr/lib/ssl/misc
CAplPATH=install/files/usr/lib/ssl/misc
DESTROOTPATH=/var/www
DESTFOLDER=cert
DESTPATH=$DESTROOTPATH/$DESTFOLDER
SSLCNF=/etc/ssl/openssl.cnf
RADCERTVALIDITY=3650 #ten years
CACERTVALIDITY=3650 #ten years
PATHDIRMODE=0744
DIRMODE=0500

CAKEY=cakey.pem
CAREQ=careq.pem
CACERT=cacert.pem

RADIUSKEY=radius_key.pem
RADIUSREQ=radius_req.pem
RADIUSCERT=radius_cert.pem

EAPCONF=/etc/freeradius/eap.conf

############################################
#Get the name of the client firm
read -p "Enter the name of the client (CA common name): " CLIENTNAME


############################################
#Modification of the configuration openssl.cnf
sed\
	-e "s|./demoCA|$DESTPATH|"\
	-e "/\s*certificate\s*=/c certificate = \$dir/private/cacert.pem\t"\
#	-e '/countryName_default/c countryName_default		= FR'\
#	-e '/stateOrProvinceName_default/c stateOrProvinceName_default	= France'\
	-i $SSLCNF
#if grep -qs "localityName_default" $SSLCNF 
#then
#	sed\
#		-e '/localityName_default/c localityName_default		= Nancy'\
#		-i $SSLCNF
#else
#	sed\
#		-e '/Locality Name/a localityName_default		= Nancy'\
#		-i $SSLCNF
#fi
#sed\
#	-e '/0.organizationName_default/c 0.organizationName_default	= BHConsulting'\
#	-i $SSLCNF
#


############################################
#Creation of the needed repertories

if test ! -d $DESTROOTPATH
then
	mkdir -p $DESTROOTPATH -m $PATHDIRMODE
fi

mkdir $DESTPATH -m $DIRMODE;
chown freerad $DESTPATH
mkdir "${DESTPATH}/certs" -m $DIRMODE;
chown freerad $DESTPATH/certs
mkdir "${DESTPATH}/crl" -m $DIRMODE ;
chown freerad $DESTPATH/crl
mkdir "${DESTPATH}/newcerts" -m $DIRMODE;
chown freerad $DESTPATH/newcerts
mkdir "${DESTPATH}/private" -m $DIRMODE;
chown freerad $DESTPATH/private
mkdir "${DESTPATH}/users" -m $PATHDIRMODE;
chown freerad $DESTPATH/users
touch $DESTPATH/index.txt
echo "01" > $DESTPATH/crlnumber

############################################
#Creation of the Authority Certificate (CA)

#openssl req -config /etc/ssl/openssl.cnf -new -keyout $DESTPATH/private/$CAKEY -out $DESTPATH/$CAREQ

openssl genrsa -out $DESTPATH/private/$CAKEY 4096 

openssl req -new -key $DESTPATH/private/$CAKEY \
	-subj /countryName=FR/stateOrProvinceName=France/localityName=Nancy/organizationName=BHConsulting/commonName=$CLIENTNAME/ \
	-out $DESTPATH/$CAREQ


openssl ca -config /etc/ssl/openssl.cnf \
	-create_serial -out $DESTPATH/private/$CACERT -days $CACERTVALIDITY -batch \
	-keyfile $DESTPATH/private/$CAKEY -selfsign \
	-extensions v3_ca \
	-infiles $DESTPATH/$CAREQ


#This instruction is replaced by the tree above instructions.
#$CAplPATH/CA.pl -newca




############################################
#Creation of the radius certificate
openssl genrsa -out $DESTPATH/private/$RADIUSKEY 4096

openssl req -config /etc/ssl/openssl.cnf -new -key $DESTPATH/private/$RADIUSKEY \
	-subj /countryName=FR/stateOrProvinceName=France/localityName=Nancy/organizationName=BHConsulting/commonName=`hostname`/ \
	-out $DESTPATH/private/$RADIUSREQ -days $RADCERTVALIDITY
openssl ca -config /etc/ssl/openssl.cnf -policy policy_anything -out $DESTPATH/private/$RADIUSCERT -batch -infiles $DESTPATH/private/$RADIUSREQ


############################################
#First CRL Generation and link to permit revocation verifications

openssl ca -config /etc/ssl/openssl.cnf -gencrl -out $DESTPATH/crl/crl.pem

HASH=`openssl x509 -noout -hash -in $DESTPATH/private/$CACERT`
ln -s $DESTPATH/private/$CACERT $DESTPATH/certs/$HASH.0
ln -s $DESTPATH/crl/crl.pem $DESTPATH/certs/$HASH.r0

############################################
#Now, we configure the radius.

#First, we must create those two file.
dd if=/dev/urandom of=$DESTPATH/random count=2
openssl dhparam -check -text -5 1024 -out $DESTPATH/dh

#Then we change the eap.conf file

#changes for TLS section
sed\
        -e "/certdir =/i \\\t\t\tconfdir = $DESTPATH"\
        -e "/certdir =/c \\\t\t\tcertdir = \${confdir}"\
        -e "/cadir =/c \\\t\t\tcadir = \${confdir}/private"\
        -e "/private_key_password =/c \\\t\t\tprivate_key_password ="\
        -e "/private_key_file =/c \\\t\t\tprivate_key_file = \${cadir}/$RADIUSKEY"\
        -e "/^\s*certificate_file =/c \\\t\t\tcertificate_file = \${cadir}/$RADIUSCERT"\
        -e "/^\s*CA_file =/c \\\t\t\tCA_file = \${cadir}/$CACERT"\
        -e "/dh_file =/c \\\t\t\tdh_file = \${certdir}/dh"\
        -e "/check_crl =/c \\\t\t\tcheck_crl = yes"\
        -e "/^\s*#*CA_path =/c \\\t\t\tCA_path = \${certdir}/certs"\
        -e "/^\s*#*\s*check_cert_cn =/c \\\t\t\tcheck_cert_cn = %{User-Name}"\
        -i $EAPCONF


