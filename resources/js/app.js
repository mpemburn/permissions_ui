window.$ = require('jquery');
require('./bootstrap');
require('alpinejs');

import Modal from './modal';
import PermissionsManager from './permissions-manager';
import UserRolesManager from './user-roles-manager';

let modal = new Modal();

new PermissionsManager({
    modal: modal
});

new UserRolesManager({
    modal: modal
});

