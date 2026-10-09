<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'moderator' => [
            'title'       => 'Moderator',
            'description' => 'Moderator verifikasi keanggotaan, kurasi direktori, dan penanganan blacklist.',
        ],
        'editor' => [
            'title'       => 'Editor Konten',
            'description' => 'Pengelola konten beranda, banner, pengumuman, dan pengaturan footer.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'user' => [
            'title'       => 'User / Member',
            'description' => 'General users of the site. Often customers.',
        ],
        'event_staff' => [
            'title'       => 'Event Staff',
            'description' => 'Petugas lapangan check-in dan scanner tiket kegiatan KOMEO.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta-level features.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'admin.access'        => 'Can access the sites admin area',
        'admin.settings'      => 'Can access the main site settings',
        'users.manage-admins' => 'Can manage other admins and roles',
        'users.create'        => 'Can create new non-admin users',
        'users.edit'          => 'Can edit existing non-admin users',
        'users.delete'        => 'Can delete existing non-admin users',
        'content.manage'      => 'Dapat mengelola konten beranda dan footer',
        'blacklist.manage'    => 'Dapat mengelola daftar blacklist event',
        'members.verify'      => 'Dapat memverifikasi dan menyetujui member',
        'directory.manage'    => 'Dapat mengelola direktori dan featured member',
        'events.manage'       => 'Dapat mengelola kegiatan dan partisipan event',
        'events.staff'        => 'Dapat melakukan check-in dan scan tiket pada kegiatan yang ditugaskan',
        'beta.access'         => 'Can access beta-level features',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'content.*',
            'blacklist.*',
            'members.*',
            'directory.*',
            'events.*',
            'beta.*',
        ],
        'admin' => [
            'admin.*',
            'users.create',
            'users.edit',
            'content.*',
            'blacklist.*',
            'members.*',
            'directory.*',
            'events.*',
            'beta.access',
        ],
        'moderator' => [
            'admin.access',
            'members.verify',
            'directory.manage',
            'blacklist.manage',
        ],
        'editor' => [
            'admin.access',
            'content.manage',
        ],
        'event_staff' => [
            'admin.access',
            'events.staff',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'beta.access',
        ],
        'user' => [],
        'beta' => [
            'beta.access',
        ],
    ];
}
