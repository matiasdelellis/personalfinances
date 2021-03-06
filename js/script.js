(function (OC, window, $, undefined) {
'use strict';

$(document).ready(function () {

var translations = {
    newAccount: $('#new-account-string').text()
};

// this Accounts object holds all our accounts
var Accounts = function (baseUrl) {
    this._baseUrl = baseUrl;
    this._accounts = [];
    this._activeAccount = undefined;
};

Accounts.prototype = {
    load: function (id) {
        var self = this;
        this._accounts.forEach(function (account) {
            if (account.id == id) {
                account.active = true;
                self._activeAccount = account;
            } else {
                account.active = false;
            }
        });
    },
    getActive: function () {
        return this._activeAccount;
    },
    unsetActive: function () {
        this._activeAccount = undefined;
        this._accounts.forEach(function (account) {
            account.active = false;
        });
    },
    removeActive: function () {
        var index;
        var deferred = $.Deferred();
        var id = this._activeAccount.id;
        this._accounts.forEach(function (account, counter) {
            if (account.id == id) {
                index = counter;
            }
        });

        if (index !== undefined) {
            // delete cached active account if necessary
            if (this._activeAccount === this._accounts[index]) {
                delete this._activeAccount;
            }

            this._accounts.splice(index, 1);

            $.ajax({
                url: this._baseUrl + '/' + id,
                method: 'DELETE'
            }).done(function () {
                deferred.resolve();
            }).fail(function () {
                deferred.reject();
            });
        } else {
            deferred.reject();
        }
        return deferred.promise();
    },
    create: function (account) {
        var deferred = $.Deferred();
        var self = this;
        $.ajax({
            url: this._baseUrl,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(account)
        }).done(function (account) {
            self._accounts.push(account);
            self._activeAccounts = account;
            self.load(account.id);
            deferred.resolve();
        }).fail(function () {
            deferred.reject();
        });
        return deferred.promise();
    },
    getAll: function () {
        return this._accounts;
    },
    loadAll: function () {
        var deferred = $.Deferred();
        var self = this;
        $.get(this._baseUrl).done(function (accounts) {
            self._activeAccounts = undefined;
            self._accounts = accounts;
            deferred.resolve();
        }).fail(function () {
            deferred.reject();
        });

        return deferred.promise();
    },
    updateActive: function (name, type, initial) {
        var account = this.getActive();
        account.name = name;
        account.type = type;
        account.initial = initial;

        return $.ajax({
            url: this._baseUrl + '/' + account.id,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(account)
        });
    }
};

// this will be the view that is used to update the html
var View = function (accounts) {
    this._accounts = accounts;
};

View.prototype = {
    renderContent: function () {
        var source = $('#content-tpl').html();
        var template = Handlebars.compile(source);
        var html = template({account: this._accounts.getActive()});

        $('#transactions').html(html);

        if (!this._accounts.getActive()) {
            var deferred = $.Deferred();
            var timestamp = (Date.now() / 1000) - 30*24*60*60;
            var categories = [];
            $.get(OC.generateUrl('/apps/personalfinances/report/' + timestamp)).done(function (report) {
                report.forEach(function (row) {
                    if (row.cat_parent_name)
                        var cat_name = row.cat_parent_name + " > " + row.cat_name;
                    else
                        var cat_name = row.cat_name;

                    var found = false;
                    for (var i = 0; i < categories.length; i++) {
                        if (categories[i].id == row.id) {
                            categories[i].amount += parseFloat(row.amount);
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        categories.push({
                            name: cat_name,
                            id: row.id,
                            amount: parseFloat(row.amount)
                        });
                    }
                });

                categories.sort (function (a, b) {
                    return a.amount - b.amount;
                });

                var labelsA = [];
                var dataA = [];
                for (var i = 0; i < categories.length; i++) {
                    if (categories[i].amount < 0)
                        categories[i].amount*=-1;
                    else
                        continue;
                    labelsA.push(categories[i].name);
                    dataA.push(categories[i].amount);
                }
                var ctx = $("#reportChart");
                var expensesChart = new Chart(ctx, {
                    type: 'bar',
                    options: {
                        legend: {
                            display: false
                        },
                        scaleShowLabels : false
                    },
                    data: {
                        labels: labelsA,
                        datasets: [{
                            //label: "Last 30 days",
                            data: dataA,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'

                            ],
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ]
                        }]
                    }
                });

                 // Fill table.
                $('#report_table').DataTable({
                    paging: false,
                    data: report,
                    columns: [
                        { "data": "date", "title": "Date",
                            "render": function (data, type, row) {
                                if (type === 'display') {
                                    var date = new Date(data*1000);
                                    date = date.toLocaleString("es", {
                                                               year    : 'numeric',
                                                               month   : '2-digit',
                                                               day     : '2-digit',
                                                               timeZone: 'UTC'});
                                    return date;
                                }
                                else {
                                    return data;
                                }
                            }
                        },
                        { "data": "cat_name", "title": "Category",
                            "render": function (data, type, row) {
                                if (row.cat_parent_name)
                                    var cat_name = row.cat_parent_name + " > " + row.cat_name;
                                else
                                    var cat_name = row.cat_name;
                                return cat_name;
                            }
                        },
                        { "data": "info", "title": "Info"},
                        { "data": "amount", "title": "Amount",
                            "render": function (data, type, row) {
                                if (type === 'display') {
                                    return '$ ' + parseFloat(data).toFixed(2);
                                }
                                else {
                                    return data;
                                }
                            }
                        }
                    ]
                });
                deferred.resolve();
            }).fail(function () {
                deferred.reject();
            });
            deferred.promise();

        }

        if (this._accounts.getActive()) {
            var deferred = $.Deferred();
            $.get(OC.generateUrl('/apps/personalfinances/balance/'+this._accounts.getActive().id)).done(function (balance) {
                $('#total-balance').html("$ " + parseFloat(balance).toFixed(2));
                deferred.resolve();
            }).fail(function () {
                deferred.reject();
            });
            deferred.promise();
        }

        if (this._accounts.getActive()) {
            var deferred = $.Deferred();
            var self = this;
            $.get(OC.generateUrl('/apps/personalfinances/transactions/'+this._accounts.getActive().id)).done(function (transactions) {
                $('#transactions_table').DataTable({
                    paging: false,
                    searching: false,
                    data: transactions,
                    columns: [
                        { "data": "date", "title": "Date",
                            "render": function (data, type, row) {
                                if (type === 'display') {
                                    var date = new Date(data*1000);
                                    date = date.toLocaleString("es", {
                                                               year    : 'numeric',
                                                               month   : '2-digit',
                                                               day     : '2-digit',
                                                               timeZone: 'UTC'});
                                    return date;
                                }
                                else {
                                    return data;
                                }
                            }
                        },
                        { "data": "info", "title": "Info"},
                        { "data": "amount", "title": "Amount",
                            "render": function (data, type, row) {
                                if (type === 'display') {
                                    return '$ ' + parseFloat(data).toFixed(2);
                                }
                                else {
                                    return data;
                                }
                            }
                        }
                    ]
                });
                deferred.resolve();
            }).fail(function () {
                deferred.reject();
            });
            deferred.promise();
        }

        // handle saves
        /*var textarea = $('#app-content textarea');
        var self = this;
        $('#app-content button').click(function () {
            var content = textarea.val();
            var title = content.split('\n')[0]; // first line is the title

            self._accounts.updateActive(title, content).done(function () {
                self.render();
            }).fail(function () {
                alert('Could not update note, not found');
            });
        });*/
    },
    renderNavigation: function () {
        var source = $('#navigation-tpl').html();
        var template = Handlebars.compile(source);
        var accounts = this._accounts.getAll();
        var abanks = [], banks = [];

        /* Get Banks names on array */
        $.each(accounts, function(i, val) {
            if ($.inArray(val.bank_name, abanks) == -1) {
                abanks.push(val.bank_name);
            }
        });
        /* Get accounts from banks on array */
        $.each(abanks, function(i, val) {
            var obanks = $.grep(accounts, function(oval, index) {
                return val == oval.bank_name;
            });
            banks.push({
                name: val,
                accounts: obanks
            });
        });

        var html = template({banks: banks});
        $('#app-navigation ul').html(html);

        // load a account
        $('#expense-report').click(function () {
            self._accounts.unsetActive();
            self.render();
        });

        // create a new account
        var self = this;
        $('#new-account').click(function () {
            var account = {
                name: translations.newAccount,
                type: 0,
                initial: 22.55
            };

            self._accounts.create(account).done(function() {
                self.render();
                //$('#editor textarea').focus();
            }).fail(function () {
                alert('Could not create account');
            });
        });

        // show app menu
        $('#app-navigation .app-navigation-entry-utils-menu-button').click(function () {
            var entry = $(this).closest('.note');
            entry.find('.app-navigation-entry-menu').toggleClass('open');
        });

        // delete a note
        $('#app-navigation .note .delete').click(function () {
            var entry = $(this).closest('.note');
            entry.find('.app-navigation-entry-menu').removeClass('open');

            self._accounts.removeActive().done(function () {
                self.render();
            }).fail(function () {
                alert('Could not delete account, not found');
            });
        });

        // load a account
        $('#app-navigation .note > a').click(function () {
            var id = parseInt($(this).parent().data('id'), 10);
            self._accounts.load(id);
            self.render();
            //$('#editor textarea').focus();
        });
    },
    render: function () {
        this.renderNavigation();
        this.renderContent();
    }
};

var accounts = new Accounts(OC.generateUrl('/apps/personalfinances/accounts'));
var view = new View(accounts);
accounts.loadAll().done(function () {
    view.render();
}).fail(function () {
    alert('Could not load accounts');
});


});

})(OC, window, jQuery);