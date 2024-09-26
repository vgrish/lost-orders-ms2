{$_modx->lexicon->load('minishop2:default')}
{$_modx->lexicon->load('minishop2:product')}
{$_modx->lexicon->load('minishop2:cart')}
{var $style = [
'logo' => 'display:block;margin: auto;',
'a' => 'color:#348eda;',
'p' => 'font-family: Arial;color: #666666;font-size: 12px;',
'h' => 'font-family:Arial;color: #111111;font-weight: 200;line-height: 1.2em;margin: 40px 20px;',
'h1' => 'font-size: 36px;',
'h2' => 'font-size: 28px;',
'h3' => 'font-size: 22px;',
'th' => 'font-family: Arial;text-align: left;color: #111111;',
'td' => 'font-family: Arial;text-align: left;color: #111111;',
]}

{var $site_url = ('site_url' | option) | preg_replace : '#/$#' : ''}
{var $assets_url = 'assets_url' | option}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{'site_name' | option}</title>
</head>
<body style="margin:0;padding:0;background:#f6f6f6;">
<div style="height:100%;padding-top:20px;background:#f6f6f6;">
    <!-- body -->
    <table class="body-wrap" style="padding:0 20px 20px 20px;width: 100%;background:#f6f6f6;margin-top:10px;">
        <tr>
            <td></td>
            <td class="container" style="border:1px solid #f0f0f0;background:#ffffff;width:800px;margin:auto;">
                <div class="content">
                    <table style="width:100%;">
                        <tr>
                            <td>
                                <h3 style="{$style.h}{$style.h3}">
                                    {block 'title'}
                                        У вас отличный вкус!❤️

                                        Мы заметили, что вы добавили в корзину товары и, возможно, просто отвлеклись от оформления заказа.

                                        Товары все еще ждут вас в корзине, успейте оформить заказ, пока всё не раскупили☺️

                                    {/block}
                                </h3>

                                {block 'products'}
                                    <table style="width:90%;margin:auto;">
                                        <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th style="{$style.th}">{'ms2_cart_title' | lexicon}</th>
                                            <th style="{$style.th}">{'ms2_cart_count' | lexicon}</th>
                                            <th style="{$style.th}">{'ms2_cart_cost' | lexicon}</th>
                                        </tr>
                                        </thead>
                                        {foreach $products as $product}
                                            <tr>
                                                <td style="{$style.th}">
                                                    {if $product.thumb?}
                                                        <img src="{$site_url}{$product.image}" width="90px">
                                                    {else}
                                                    {/if}
                                                </td>
                                                <td style="{$style.th}">
                                                    {if $product.id?}
                                                        <a href="{$product.id | url : ['scheme' => 'full']}"
                                                           style="{$style.a}">
                                                            {$product.name}
                                                        </a>
                                                    {else}
                                                        {$product.name}
                                                    {/if}
                                                    {if $product.options?}
                                                        {foreach $product.options as $key => $option}

                                                            {if $key in ['modification','modifications','msal']}{continue}{/if}

                                                            {set $caption = $product[$key ~ '.caption']}
                                                            {set $caption = $caption ? $caption : ('ms2_product_' ~ $key) | lexicon}

                                                            {if $option is array}

                                                                <div class="small">Размер: {$option | join : '; '}</div>
                                                            {else}

                                                                <div class="small">Размер: {$option}</div>
                                                            {/if}

                                                        {/foreach}
                                                    {/if}
                                                </td>
                                                <td style="{$style.th}">{$product.count} {'ms2_frontend_count_unit' | lexicon}</td>
                                                <td style="{$style.th}">{$product.price} {'ms2_frontend_currency' | lexicon}</td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                    <h3 style="{$style.h}{$style.h3}">
                                        {'ms2_frontend_order_cost' | lexicon}:
                                        {if $total.delivery_cost}
                                            {$total.cart_cost} {'ms2_frontend_currency' | lexicon} + {$total.delivery_cost}
                                            {'ms2_frontend_currency' | lexicon} =
                                        {/if}
                                        <strong>{$total.cost}</strong> ₽
                                    </h3>
                                {/block}
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /content -->
            </td>
            <td></td>
        </tr>
    </table>
    <!-- /body -->
    <!-- footer -->
    <table style="clear:both !important;width: 100%;">
        <tr>
            <td></td>
            <td class="container">
                <!-- content -->
                <div class="content">
                    <table style="width:100%;text-align: center;">
                        <tr>
                            <td align="center">
                                <p style="{$style.p}">
                                    {block 'footer'}
                                        <a href="{$site_url}" style="color: #999999;">
                                            {'site_name' | option}
                                        </a>
                                    {/block}
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /content -->
            </td>
            <td></td>
        </tr>
    </table>
    <!-- /footer -->

    <div style="text-align:center;padding:0 20px;margin-bottom:30px;">
        <a href="{$order_url}" target="_blank">перейти к покупкам</a>
        <br>
    </div>

</div>
</body>
</html>
