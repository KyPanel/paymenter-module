# KyPanel for Paymenter

Version 1.0.0 · Paymenter 1.x

Server extension for selling hosting accounts on KyPanel servers.

## Install

1. Copy `extensions/Servers/KyPanel` to the same folder in your Paymenter.
2. In KyPanel, open Ky Admin › Development › Manage API Tokens and create a token with
   **accounts** (read and change) and **packages** (read). Limit it to the IP of your Paymenter server.
3. In Paymenter, go to Admin › Extensions › Servers, enable **KyPanel** and fill in the hostname,
   port 7711 and the token.
4. On each product, pick the server and the KyPanel package.

Creating, suspending, unsuspending, terminating and package changes run on the server.
The service page has a "Log in to KyPanel" button that signs the customer in with a one-time link.

License: MIT

Documentation: https://docs.kypanel.com/ · Support: https://my.kypanel.com/tickets/new
