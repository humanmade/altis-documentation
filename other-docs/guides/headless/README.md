# Headless Frontends

Altis supports fully-decoupled headless frontends, such as single-page apps (SPAs). Rather than being rendered directly via the theming system, these frontends are rendered by a separate frontend; this may be in-browser rendering, or a third-party system such as Node.js.


## Architecture types

There are generally three types of project architecture concerning frontends:

* Fully coupled: The backend system and frontend system are a single monolithic application running on the same server. Most traditional web apps follow this pattern.
* Fully decoupled: The backend system and frontend system are completely separate applications running on separate servers. These systems communicate purely via web protocols, typically a REST API.
* Semi-decoupled: The backend system and frontend system are separated, but form parts of the same application typically running on the same server. These systems communicate primarily via web prococols (typically a REST API), but may also pass data internally inside the application.


### Fully coupled

A fully coupled architecture is the traditional architecture used for web applications.

With a fully coupled system, the frontend is deeply-integrated into the backend, and is managed in conjunction with the backend. Often a single development team will be responsible for the entire application. The frontend and backend are both built in PHP.

Altis is built and configured for a fully coupled frontend by default, through the theming system. Consult the [theming documentation](docs://getting-started/first-theme.md) for further information about building themes.


### Fully decoupled

A fully decoupled architecture is a commonly-used architecture for large web apps built by large teams. Treating the frontend and backend as completely separate systems allows teams to work completely independently.

With fully decoupled systems, the frontend is built entirely separately, and is typically managed by a separate frontend team. The frontend can be built in any desired language, regardless of the backend being powered by PHP. The frontend is also typically run on entirely separate infrastructure, as the performance characteristics may be entirely different to the backend.

Altis provides a REST API to allow building fully decoupled frontends. With a fully decoupled architecture, the frontend is hosted and managed on third-party infrastructure, with Altis acting as the backend only.


### Semi-decoupled

A semi-decoupled architecture is a hybrid architecture incorporating parts of both coupled and decoupled architectures. It allows many of the benefits of decoupled architectures to be realized while avoiding much of the overhead.

With a semi-decoupled system, the frontend is built as a part of the application, but has only a few, well-defined integration points between the frontend and backend. These are kept small and manageable to allow teams to work mostly independently. The frontend is built in JavaScript, allowing in-browser rendering in combination with [server-side rendering](ssr.md). Both the frontend and backend run on the same infrastructure.

Altis provides a REST API to allow building semi-decoupled frontends, in addition to tools and libraries to facilitate building these frontends. The [Building a React App guide](react-app.md) guide walks through building a semi-decoupled React app from scratch.

Additionally, Altis provides infrastructure to allow for [server-side rendering](ssr.md).


## Deciding on architecture

Before embarking on a headless architecture, decide exactly how you want to build and manage your project.

Headless frontends can be beneficial in allowing separate teams and skills to handle building a site's frontend. However, they also represent a significant amount of work, as it often requires rebuilding significant pieces of functionality to better fit the decoupled architecture. For example, routing requests is a mostly-solved problem with traditional coupled architectures, but can require recreating the routing system in a decoupled architecture.

Additionally, this may require separate and complex infrastructure for serving web requests. This can increase running and maintenance costs, as multiple systems need to be run.

Many of the features provided by the CMS module (and WordPress) are not available in decoupled frontends, as they are tied to the theme frontend system. This includes the Customizer, the admin bar, and some of the developer tooling. Many other features assumed to work by users may require significant work to integrate into decoupled frontends, including post previews and varying behavior for authenticated/authorized users.

Decoupling can also have overhead as compared to monolithic systems, as data needs to be passed between multiple systems. This can cause both technical overhead with latency on round-trips, as well as mental overhead during development, as systems need to be specifically built to accomodate this. Consider whether the additional development time is worth the cost of decoupling.

In many cases, a semi-decoupled architecture can achieve the best of both worlds. This uses parts of the theming system for some frontend functionality, while moving much of the work off to a separate frontend. This can help avoid duplication across systems while still providing many of the benefits of a decoupled architecture. In combination with server-side rendering, data can be generated at the same time as rendering the frontend for users, providing an fluid interactive experience to users with little overhead.

Generally, we recommend using a semi-decoupled architecture, but each case is different. The Altis team can provide consultancy and support to guide you through this process.
