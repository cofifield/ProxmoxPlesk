# ProxmoxPlesk
Plesk template file for reverse proxying Proxmox through the built in frame forwarding. This enables properly proxied websocket connections and allows the VNC console to work remotely. This also allows the built in letsencrypt to retain its functionality. This works by directly outputting the user supplied redirect URL into the nginx configuration file instead of using Plesk's bult in backend port redirection. It additionally add the required proxy_set_header Upgrade $http_upgrade; and proxy_set_header Connection "upgrade"; directives required to enable the VNC console over a proxied connection. [Referenced](https://pve.proxmox.com/wiki/Web_Interface_Via_Nginx_Proxy)

## Notice
This might break other frame forwarding setups you already have configured. I haven't tested it with a "standard" proxy connection. You probably shouldn't use this file if you have production services being proxied. This also bypasses any collected webstats.

If you are interested in looking into the changes I have made, the original file is located here:
/opt/psa/admin/conf/templates/domain/nginxForwarding.php

# Setup
1) SSH into your Plesk instance.
2) Transfer the nginxForwarding.php file to your home user directory
3) Create the required directories:
```
sudo mkdir /opt/psa/admin/conf/templates/custom
sudo mkdir /opt/psa/admin/conf/templates/custom/domain
```
4) Copy the template file:
```
sudo cp ~/nginxForwarding.php /opt/psa/admin/conf/templates/custom/domain/nginxForwarding.php
```
5) Regenerate the vHost templates:
```
sudo /usr/local/psa/admin/sbin/httpdmng --reconfigure-all
```
6) Configure your domain/subdomain "Hosting Settings" to use frame forwarding
7) Input your Proxmox web interface address in the "Destination address" field EX: http://192.168.1.x:8006/
8) Enable SSL/TLS support and Permanent SEO-safe 301 redirect from HTTP to HTTPS in the "Security" settings

# Removal
1) Remove the custom template directory:
```
sudo rm -rf /opt/psa/admin/conf/templates/custom
```
2) Regenerate the vHost templates:
```
sudo /usr/local/psa/admin/sbin/httpdmng --reconfigure-all
```
