class { 'systools': }
class { 'apache': }
class { 'php':
  development => true
}
class { 'drush': }
class { 'postfix': }
class { 'mongodb': }


class { 'mysql':
  local_only     => true,
  hostname => "{{ hostname }}"
}

apache::vhost { "drupal": }
