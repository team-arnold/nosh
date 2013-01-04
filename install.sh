if [ -d ~/nosh ]
then
  echo -e "\033[0;33mYou already have nosh installed.\033[0m You'll need to remove ~/nosh if you want to install"
  exit
fi

echo -e "\033[0;34mCloning nosh...\033[0m"
hash git >/dev/null && /usr/bin/env git clone https://github.com/WKLive/nosh.git ~/nosh || {
  echo -e "git not installed."
  exit
}

if pushd ~/nosh > /dev/null;
then
  curl -s getcomposer.org/installer | php -d detect_unicode=Off -d date.timezone=UTC
  
  ./composer.phar install

  echo -e "\033[0;34mSymlinking nosh to bin\033[0m"
  if [ -h /usr/bin/nosh ]
  then
    echo -e "\033[0;33mExisting symlink detected, please confirm removal of it\033[0m"
    sudo rm /usr/bin/nosh
  fi
  sudo ln -s ~/nosh/nosh.php /usr/bin/nosh
  echo -e "\033[0;34mAdding nosh to your PATH...\033[0m"
  export PATH="$PATH:$NOSHLOCATION/nosh:/usr/local/bin"

  popd > /dev/null;
fi

echo -e "\033[0;32m"'                       __   '"\033[0m"
echo -e "\033[0;32m"'    ____  ____   _____/ /_  '"\033[0m"
echo -e "\033[0;32m"'   / __ \/ __ \ / ___/ __ \ '"\033[0m"
echo -e "\033[0;32m"'  / / / / /_/ /(__  ) / / / '"\033[0m"
echo -e "\033[0;32m"' /_/ /_/\____/ /___/_/ /_/  '"\033[0m"
echo -e "\n \033[0;32m....is now installed and ready to be used.\033[0m"

