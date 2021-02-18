export default class UserRolesManager {
    constructor(options) {
        this.csrf = $('[name="_token"]');
        this.bToken = $('[name="b_token"]');
        this.editForm = $('#user_role_edit_form');
        this.editButtons = $('*[data-edit]');
        this.editUserName = $('#user_name');
        this.editUserId = $('input[name="user_id"]');
        this.selectedRoleName = null;
        this.selectedPermissionName = null;
        this.roleRows = null;
        this.editorRoleCheckboxes = $('[data-type="role"]');
        this.editorPermissionCheckboxes = $('[data-type="permission"]');
        this.saveButton = $('#save_user_roles');
        this.apiAction = $('#modal_form').attr('action');
        this.getAssignedEnpoint = $('[name="get_assigned_endpoint"]').val();
        this.permissionsAreAssignedMessage = $('#permissions_are_assigned');
        this.errorMessage = $('#user_roles_error');

        if (options.modal) {
            this.modal = options.modal;
            this.resetModal();
        }

        if (this.editForm.is('*')) {
            this.addEventListeners();
        }
    }

    resetModal() {
        // Uncheck all "Roles" checkboxes
        this.editorRoleCheckboxes.each(function () {
            $(this).prop('checked', false);
            $(this).prop('disabled', '');
        });
        // Uncheck all "Permissions" checkboxes
        this.editorPermissionCheckboxes.each(function () {
            let checkbox = $(this);
            let listItem = checkbox.parent();
            checkbox.prop('checked', false).show();
            listItem.removeClass('text-gray-400 pl-6');
        });

        this.permissionsAreAssignedMessage.hide();
    }

    readRowEntities(row, entityType) {
        let self = this;
        let collection = row.find('li[data-type="' + entityType + '"]');

        this.entityType = entityType;
        collection.each(function () {
            let entityName = $(this).data('entityName');
            self.selectDialogCheckboxes(self.entityType, entityName);
            if (self.entityType === 'role') {
                self.retrievePermissionsOwnedByRole(entityName, true);
            }
        });
    }

    selectDialogCheckboxes(entityType, entityName) {
        let self = this;
        // Reference the correct checkboxes for roles or permissions
        this.editorCheckboxes = $('input[data-type="' + entityType + '"]');

        if (typeof (entityName) !== "undefined") {
            this.entityName = entityName;
            this.entityType = entityType;
            this.editorCheckboxes.each(function () {
                if ($(this).val() === self.entityName) {
                    $(this).prop('checked', true);
                }
                if (self.entityType === 'role' && $(this).val() === 'Administrator') {
                    // If the current user is an Administrator, make so this can't be changed
                    self.disableAdminChangeForCurrentUser($(this));
                }
            });
        }
    }

    disableAdminChangeForCurrentUser(checkbox) {
        let currentUserId = $('input[name="current_user_id"]').val();
        let disabled = (currentUserId === this.editUserId.val()) ? 'disabled' : '';

        checkbox.prop('disabled', disabled);
    }

    // Call back end to see if this role has associated permissions
    retrievePermissionsOwnedByRole(roleName, shouldShow) {
        let self = this;

        this.shouldShow = shouldShow;
        $.ajax({
            url: this.getAssignedEnpoint,
            type: 'GET',
            datatype: 'json',
            data: 'role_name=' + roleName,
            headers: {
                'X-CSRF-TOKEN': this.csrf.val(),
                'Authorization': 'Bearer ' + this.bToken.val()
            },
            success: function (response) {
                self.togglePermissionsOwnedByRole(response, self.shouldShow);
            },
            error: function (data) {
            }
        });
    }

    togglePermissionsOwnedByRole(response, shouldShow) {
        let self = this;

        this.shouldShow = shouldShow;
        if (response.permissions) {
            this.assignedPermissions = response.permissions
            $('input[data-type="permission"]').each(function () {
                let checkbox = $(this);
                let listItem = checkbox.parent();
                if ($.inArray(checkbox.val(), self.assignedPermissions) !== -1) {
                    // If this permission is already assigned
                    // hide the checkbox, gray it out, and show message
                    checkbox.toggle(! self.shouldShow);
                    listItem.toggleClass('text-gray-400 pl-6', self.shouldShow);
                    self.permissionsAreAssignedMessage.toggle(self.shouldShow);
                }
            });
        }
    }

    addEventListeners() {
        let self = this;

        this.editButtons.on('click', function (evt) {
            let name = $(this).data('name');
            let userId = $(this).data('edit');
            let row = $(this).parent().parent();

            //Reset all elements in edit dialog
            self.resetModal();

            self.editUserName.html(name);
            self.editUserId.val(userId);
            // Find the lists of roles and permission in the row
            // and use this to check the appropriate boxes in the dialog
            self.readRowEntities(row, 'role')
            self.readRowEntities(row, 'permission')

            self.modal.toggleModal();
        });

        this.editorRoleCheckboxes.on('click', function () {
            self.retrievePermissionsOwnedByRole($(this).val(), this.checked);
        });

        this.saveButton.on('click', function () {
            let dataValue = self.editForm.serialize();

            $.ajax({
                url: self.apiAction,
                type: 'POST',
                datatype: 'json',
                data: dataValue,
                headers: {
                    'X-CSRF-TOKEN': self.csrf.val(),
                    'Authorization': 'Bearer ' + self.bToken.val()
                },
                success: function (response) {
                    self.modal.toggleModal();

                    document.location.reload();
                },
                error: function (data) {
                    self.errorMessage.html(data.responseJSON.error)
                        .removeClass('opacity-0')
                        .fadeOut(5000, function () {
                            $(this).addClass('opacity-0').show();
                        });
                }
            });
        })
    }
}
