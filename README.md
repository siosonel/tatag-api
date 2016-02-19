# Tatag-API

An open platform for funding teams via a budgets-as-currency framework. See https://tatag.cc/ui/home-about for a more detailed introduction to this project.

There are related projects to the API:
- https://tatag.cc/ui/wallet: A working UI prototype for wallet and team resources. Code at https://github.com/siosonel/tatag-ui. 
- https://tatag.cc/viz/lines.php, https://tatag.cc/viz/chord_m.php, https://tatag.cc/viz/arrow.php: Prototype data visualzations. Code at https://github.com/siosonel/tatag-viz.


## Project Status

Currently in public alpha release, version 0.3-alpha. 


## Author

Edgar Sioson,  s i o s o n e l  a.t  g.m a i l 
[Donate 1.00 XTE](https://tatag.cc/ad/1)


## License

Released under the MIT License.


## Documentation

There is extensive documentation at https://tatag.cc/api/ref/docs.html.


## Requirements

*AMP stack
- Apache 2.4+
- PHP 5.5.9+
- MySQL 5.5+


## Install

1. Clone the repo: 

    ```
    > git clone https://github.com/siosonel/tatag-api.git

    // or 

    > git clone git@github.com:siosonel/tatag-api.git
    ```

2. Define HOME and fill-in database credentials in config-public.php. All other types of credentials are optional. Note that the "-public" label indicates the audience type for which the configuration is targeted, and NOT that this file's contents are meant to be publicly visible.

3. Install Composer if you don't have it yet:

    ```
    > curl -s https://getcomposer.org/installer | php

    // make it callable globally 

    > sudo mv composer.phar /usr/local/bin/composer
    ```

4. From the command line:

   ```
   // domain = whatever you're using for development, such as localhost or tatag.dev
   > tools/setup.sh [domain] 
   ```


## Testing

To-do: add php unit tests.

For now: 

    ```
    > git clone git@github.com:siosonel/addtd.git




## Contributing

See https://github.com/siosonel/tatag-api/blob/master/CONTRIBUTING.md
