class redis (
    $port = 6379,
    $lib_dir = '/var/lib/redis',
    $log_dir = '/var/log/redis',
    $max_memory = '1gb',
    $memory_policy = 'volatile-lru',
    $max_clients = false,
    $timeout = 0,
    $log_level = 'notice',
    $databases = 16,
    $snapshotting = {
        900 => 1,
        300 => 10,
        60 => 10000
    },
    $appendonly = false
) {
    File {
        owner => redis,
        group => redis,
        require => Package['redis-server'],
        notify => Service['redis-server']
    }

    package { 'redis-server':
        ensure => installed
    }

    file { $lib_dir:
        ensure => directory
    }

    file { $log_dir:
        ensure => directory
    }

    file { '/etc/redis/redis.conf':
        ensure => present,
        content => template('redis/redis.conf.erb'),
    }

    service { 'redis-server':
        ensure => running,
        enable => true,
        hasrestart => true,
        hasstatus => true
    }
}