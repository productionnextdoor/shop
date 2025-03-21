# Online Shop von HSU Shop

![Shopware 6](./documentation/img/logo.jpg "HSU Shop")

:man_student: ![HSU Shop](https://img.shields.io/:PM_Shopware_6-f0f0f0.svg "HSU Shop") :woman_student:

## Setup

To set up a new project from this base repository, follow these steps:

1. Clone the repository:
   ```sh
   git clone git@github.com:productionnextdoor/shop.git
   cd shop
   ```
2. Run the start script to create all necessary docker container
   ```sh
   make start
   ```
3. Run the init script:
   ```sh
   make init
   ```

## Makefile Targets

The `Makefile` includes the following targets:

- `help`: Show this help message
- `start`: Start the development server
- `stop`: Stop the development server
- `build`: Build the storefront and administration
- `build-storefront`: Build the storefront
- `build-administration`: Build the administration
- `cache-clear`: Clear the cache
- `exec`: Open a bash shell in the container
- `init`: Initialize the project
