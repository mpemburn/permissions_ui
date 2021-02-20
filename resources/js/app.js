import RequestAjax from "./request-ajax";
import Modal from './modal';
import PermissionsManager from './permissions-manager';
import UserRolesManager from './user-roles-manager';

let $ = require('jquery');
require('./bootstrap');
require('alpinejs');

let ajax = new RequestAjax();
let modal = new Modal();

new PermissionsManager({
    modal: modal,
    dtManager: dtManager,
    ajax: ajax
});

new UserRolesManager({
    modal: modal,
    dtManager: dtManager,
    ajax: ajax
});


