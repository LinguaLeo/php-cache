class memcached (
    $ensure = present,
    $memory = 64,
    $port = 11211,
    $listen = '127.0.0.1',
    $user = 'nobody',
    $connections_limit = 1024,
    $max_core_file_limit = false
) {
    package { 'memcached':
        ensure => $ensure ? {
            absent => absent,
            default => installed
        }
    }

    service { 'memcached':
        ensure => running,
        enable => true,
        hasrestart => true,
        hasstatus => true,
        require => Package['memcached']
    }

    file { '/etc/memcached.conf':
        ensure => $ensure ? {
            absent => absent,
            default  => file,
        },
        content => template('memcached/memcached.conf.erb'),
        require => Package['memcached'],
        notify => Service['memcached']
    }
}