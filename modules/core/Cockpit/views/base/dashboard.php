
@start('header')

    <style>

        .app-dashboard-widget {
            margin-bottom: 25px;
        }

    </style>

@end('header')

<!-- duy.ha custom: only need 1 column-->
<div class="uk-grid" data-uk-grid-margin>
    <div class="uk-width-medium-1-1">
        @trigger('admin.dashboard.main')
    </div>
    
</div>