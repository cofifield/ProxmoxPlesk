<?php
/**
 * @var Template_VariableAccessor $VAR
 * @var array $OPT
 */
?>
server {
    listen <?php echo $OPT['ipAddress']->escapedAddress . ':' . $OPT['frontendPort'] .
        ($OPT['default'] ? ' default_server' : '') . ($OPT['ssl'] ? ' ssl' : '') ?>;
    server_name <?php echo $VAR->domain->asciiName ?>;
<?php if ($VAR->domain->isWildcard): ?>
    server_name <?php echo $VAR->domain->wildcardName ?>;
<?php else: ?>
    server_name www.<?php echo $VAR->domain->asciiName ?>;
<?php endif ?>
<?php if (!$VAR->domain->isWildcard): ?>
<?php   if ($OPT['ipAddress']->isIpV6()): ?>
    server_name ipv6.<?php echo $VAR->domain->asciiName ?>;
<?php   else: ?>
    server_name ipv4.<?php echo $VAR->domain->asciiName ?>;
<?php   endif ?>
<?php endif ?>

<?php foreach ($VAR->domain->webAliases as $alias): ?>
    server_name <?php echo  $alias->asciiName ?>;
    server_name www.<?php echo $alias->asciiName ?>;
    <?php if ($OPT['ipAddress']->isIpV6()): ?>
    server_name ipv6.<?php echo $alias->asciiName ?>;
    <?php else: ?>
    server_name ipv4.<?php echo $alias->asciiName ?>;
    <?php endif ?>
<?php endforeach ?>

<?php if ($OPT['ssl']): ?>
    <?php $sslCertificate = $VAR->server->sni && $VAR->domain->forwarding->sslCertificate ?
        $VAR->domain->forwarding->sslCertificate :
        $OPT['ipAddress']->sslCertificate; ?>
    <?php if ($sslCertificate->ceFilePath): ?>
        ssl_certificate             <?php echo $sslCertificate->ceFilePath ?>;
        ssl_certificate_key         <?php echo $sslCertificate->ceFilePath ?>;
    <?php endif ?>
<?php endif ?>
	proxy_redirect off;
<?php if (!$OPT['ssl'] && $VAR->domain->forwarding->sslRedirect): ?>
        location / {
            return 301 https://$host$request_uri;
        }
    }
    <?php return ?>
<?php endif ?>

<?php if ($OPT['default']): ?>
<?php echo $VAR->includeTemplate('service/nginxSitePreview.php') ?>
<?php endif ?>

    <?php echo $VAR->domain->forwarding->nginxExtensionsConfigs ?>

    location / {
	proxy_http_version 1.1;
	proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade"; 
    <?php if ($OPT['ssl']): ?>
        proxy_pass <?php echo $VAR->domain->forwarding->redirectUrl; ?>;
    <?php else: ?>
        proxy_pass <?php echo $VAR->domain->forwarding->redirectUrl; ?>;
    <?php endif ?>
	proxy_buffering off;
	client_max_body_size 0;
	proxy_connect_timeout  3600s;
        proxy_read_timeout  3600s;
        proxy_send_timeout  3600s;
        send_timeout  3600s;
        access_log off;
    }

<?php if ($VAR->server->xPoweredByHeader) : ?>
    add_header X-Powered-By PleskLin;
<?php endif ?>
}
