{if $notification !== null}
    <div class="alert alert-{$notification.type}" role="alert">
        {$notification.message}
    </div>
{/if}
