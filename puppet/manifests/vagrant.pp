File { owner => 0, group => 0, mode => 0644 }

package {
    'vim': ensure => installed;
}

class { 'redis':
    max_memory => '64mb',
    memory_policy => 'allkeys-lru',
    snapshotting => {};
    'memcached':
        listen => '0.0.0.0'
}
