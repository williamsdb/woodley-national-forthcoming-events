<a name="readme-top"></a>


<!-- PROJECT LOGO -->
<br />
<div align="center">

<h3 align="center">u3a National Forthcoming Events Wordpress Plugin</h3>

  <p align="center">
    A plugin to display the forthcoming nation u3a events on a site works page.
    <br />
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

I was asked about the possibilty of displaying the [national u3a events](https://www.u3a.org.uk/events/educational-events#Events) on our local u3a website. I had a look and couldn't find any feed of this information so I decided to write a plugin that extracted the required data from the page and display it in tabular form.

The output looks as follows:

![](https://www.spokenlikeageek.com/wp-content/uploads/2025/07/FireShot-Capture-140-Events-woodleyu3a.neilthompson.co_.uk_.png)

Some points to note:

* events will drop off the list once passed even if they remain on the main u3a list
* the formatting of the description is taken directly from the national u3a page so if it looks wonky the plugin is just reflecting that
* **if the u3a changes they way this page works the plug will stop working!** Although I will endeavour to change the plugin to match.

<a href='https://ko-fi.com/Y8Y0POEES' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://storage.ko-fi.com/cdn/kofi5.png?v=6' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [PHP](https://php.net)
* [WordPress](https://wordpress.org)
* [site works](https://siteworks.u3a.org.uk/)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

The following will take you through the process of installing version 2 of the woodley_national_forthcoming_events plugin. 

1. Download the latest version of the plugin from [here](https://plugins.nei.lt/woodley_national_forthcoming_events/woodley_national_forthcoming_events.zip)
2. If you have version 1 install Deactivate and Delete it from the Dashboard
3. Click the Add New Plugin button at the top of the page
4. Click the Upload Plugin button
5. Find the zip file you downloaded in step 1
6. Click Install Now
7. Click Activate Now

You should see the plug looks like this:

![](https://plugins.nei.lt/30970.png)

Automatic updates are supported and when available will look like this:

![](https://plugins.nei.lt/93266.png)

### Prerequisites

Requirements are very simple, it requires the following:

1. PHP (I tested on v8.1.13)
2. a WordPress site.

### Installation

See Getting Started above.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

Edit the page that you want to display the information on, add a shortcode block and enter the following:

```[waduwn]```

The shortcode takes three parameters as follows:

```[waduwn title="0" desc="1" calendar="1"]```

Which control the following:

* title - the "Forthcoming National Events" title. Default is on.
* desc - the paragraph of text below the title. Default is on.
* calendar - whether the Add to Calendar option should be shown. Default is off.


Here it is on our Events page:

![](https://www.spokenlikeageek.com/wp-content/uploads/2025/03/Screenshot-2025-03-13-at-15.06.53.png)

<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- ROADMAP -->
## Known Issues

See the [open issues](https://github.com/williamsdb/woodley-national-forthcoming-events/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- LICENSE -->
## License

Distributed under the GNU General Public License v3.0. See `LICENSE` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Bluesky - [@spokenlikeageek.com](https://bsky.app/profile/spokenlikeageek.com)

Mastodon - [@spokenlikeageek](https://techhub.social/@spokenlikeageek)

X - [@spokenlikeageek](https://x.com/spokenlikeageek) 

Website - [https://spokenlikeageek.com](https://www.spokenlikeageek.com/)

Project link - [Github](https://github.com/williamsdb/woodley-national-forthcoming-events)

<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- ACKNOWLEDGMENTS -->
## Acknowledgments

* None.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

[![](https://github.com/williamsdb/woodley-national-forthcoming-events/graphs/contributors)](https://img.shields.io/github/contributors/williamsdb/woodley-national-forthcoming-events.svg?style=for-the-badge)

![](https://img.shields.io/github/contributors/williamsdb/woodley-national-forthcoming-events.svg?style=for-the-badge)
![](https://img.shields.io/github/forks/williamsdb/woodley-national-forthcoming-events.svg?style=for-the-badge)
![](https://img.shields.io/github/stars/williamsdb/woodley-national-forthcoming-events.svg?style=for-the-badge)
![](https://img.shields.io/github/issues/williamsdb/woodley-national-forthcoming-events.svg?style=for-the-badge)


<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/github_username/repo_name.svg?style=for-the-badge
[contributors-url]: https://github.com/github_username/repo_name/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/github_username/repo_name.svg?style=for-the-badge
[forks-url]: https://github.com/github_username/repo_name/network/members
[stars-shield]: https://img.shields.io/github/stars/github_username/repo_name.svg?style=for-the-badge
[stars-url]: https://github.com/github_username/repo_name/stargazers
[issues-shield]: https://img.shields.io/github/issues/github_username/repo_name.svg?style=for-the-badge
[issues-url]: https://github.com/github_username/repo_name/issues
[license-shield]: https://img.shields.io/github/license/github_username/repo_name.svg?style=for-the-badge
[license-url]: https://github.com/github_username/repo_name/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/linkedin_username
[product-screenshot]: images/screenshot.png
[Next.js]: https://img.shields.io/badge/next.js-000000?style=for-the-badge&logo=nextdotjs&logoColor=white
[Next-url]: https://nextjs.org/
[React.js]: https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB
[React-url]: https://reactjs.org/
[Vue.js]: https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vuedotjs&logoColor=4FC08D
[Vue-url]: https://vuejs.org/
[Angular.io]: https://img.shields.io/badge/Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white
[Angular-url]: https://angular.io/
[Svelte.dev]: https://img.shields.io/badge/Svelte-4A4A55?style=for-the-badge&logo=svelte&logoColor=FF3E00
[Svelte-url]: https://svelte.dev/
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[JQuery.com]: https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white
[JQuery-url]: https://jquery.com 
