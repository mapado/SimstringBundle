sudo apt-get update
sudo apt-get install swig g++ php5-dev make
wget -q https://github.com/jdeniau/simstring/tarball/master
tar -zxf master
cd jdeniau-simstring*/swig/php/
./prepare.sh --swig
make

echo "extension=simstring.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
