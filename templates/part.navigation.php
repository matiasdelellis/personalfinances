<!-- translation strings -->
<div style="display:none" id="new-account-string"><?php p($l->t('New account')); ?></div>

<script id="navigation-tpl" type="text/x-handlebars-template">
    <li id="expense-report"><a href="#"><?php p($l->t('Expense report')); ?></a></li>
    {{#each banks}}
        <li class="collapsible open">
            <button class="collapse"></button>
            <a href="#" class="icon-folder svg">{{ name }}</a>
            <ul>
                {{#each accounts}}
                    <li class="note with-menu {{#if active}}active{{/if}}" data-id="{{ id }}">
                        <a href="#">{{ account_name }}</a>
                        <div class="app-navigation-entry-utils">
                            <ul>
                                <li class="app-navigation-entry-utils-menu-button svg"><button></button></li>
                            </ul>
                        </div>
                        <div class="app-navigation-entry-menu">
                            <ul>
                                <li><button class="delete icon-delete svg" title="delete"></button></li>
                            </ul>
                       </div>
                    </li>
                {{/each}}
            </ul>
        </li>
    {{/each}}
    <li id="new-note"><a href="#"><?php p($l->t('New account')); ?></a></li>
</script>

<ul class="with-icon"></ul>