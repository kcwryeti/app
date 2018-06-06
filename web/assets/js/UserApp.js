'use strict';

(function (window, $, swal) {
    class UserApp {
        constructor($table) {
            this.$table = $table;
            this.url = $table.data('url');
            this.$table.on(
                'change',
                '.js-user-roles',
                this.handleRoleChange.bind(this)
            );
            this.$table.on(
                'change',
                '.js-user-is-active',
                this.handleIsActiveChange.bind(this)
            );
            this.$table.on(
                'click',
                '.js-user-delete',
                this.handleDeleteClick.bind(this)
            )
        }
        handleDeleteClick (e) {
            e.preventDefault();
            swal({
                title: "Usunac uzytkownika ?",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        let $row = $(e.currentTarget);
                        let username = this._getUsername($row);
                        let url = this._setUrl($row);
                        const $tableRow = $row.closest('tr');
                        $row.addClass('text-danger');
                        $row.find('.far').removeClass('far fa-trash-alt')
                            .addClass('fas fa-spinner')
                            .addClass('fa-spin');
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText)
                            }
                        }).then(() => {
                                $tableRow.fadeOut(3000);
                                $tableRow.remove();
                                this._pageReload();
                            }
                        );
                    }
                });

        }
        handleIsActiveChange (e) {
            let $row = $(e.currentTarget);
            let isActive = {isActive: $row.is(':checked')};
            let url = this._setUrl($row);
            let data = JSON.stringify(isActive);
            this._sendData(url,data);
        }

        handleRoleChange (e) {
            let $row = $(e.currentTarget);
            let roles = { roles: [] };
            roles.roles = this._getFormRolesData($row);
            let data = JSON.stringify(roles);
            let url = this._setUrl($row);
            this._sendData(url,data)
        }
        _pageReload() {
            let rows = $('.js-user-table tr').length;
            if (rows == 1) {
                let urlParams = new URLSearchParams(window.location.search);
                let page = parseInt(urlParams.get('page'));
                if (page > 1) {
                    urlParams.set('page', page - 1);
                    window.location.href = 'users?' + urlParams.toString();
                }

            } else {
                window.location.reload();
            }
        }
        _setUrl ($row) {
            let username = this._getUsername($row);
            return this.url + '/' + username;
        }
        _sendData (url,data) {
            $.ajax({
                url: url,
                data: data,
                type: 'PATCH',
                error: function(xhr, status, error) {
                    console.log(xhr.responseText)
                }
            })
        }
        _getUsername ($row) {
            return $row.closest('tr').children('.js-username').html();
        }
        _getFormRolesData ($row) {
            let data = [];
            $row.find(':checked').each(function () {
                data.push($(this).val());
            });
            return data;
        }
    }
    window.UserApp = UserApp
})(window, jQuery, swal);