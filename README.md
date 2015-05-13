# CodeWave Framework

CodeWave is small and easy to use framework. 

Originally the project was inspired by slim, hence used to have many similar features, but
that time is long gone. Now the framework's goal is to provide something, which other devs 
might find useful along the way or at least give them an idea, that there is always a 
different approach.

## Should I worry about the abandoned previous versions
Short answer is "No, you shouldn't", but you can if you insist. The situation with the old versions
is that, there was no community to drive them and version 2 went very, very wrong in after 2.4.0.

There ware bad decisions made, and supported and eventually they ate the version, hence could not be fixed
 without major breaks, which don't make too much sense as API changes and BC breaks should happen only in 
 major versions, as per [Semantic Versioning](http://semver.org/).
 Keeping that in mind, there was a huge need to fix the mistakes. That is why v3 is not tagged still.
 A lot of planning is going on and many experiments before something actually makes it to the commit.
 Which results in not so many commits (Note to self, stop forgetting PSR2 validation before pushing).
 
 That said, you should feel perfectly safe with v3, it is not going to change for quite some time and if it does,
 well it will receive its proper support and maintenance (v2 will also get this if someone is interested in fixing the
 things I messed up with).
 
## Notable features in v3
 - Huge decoupling: this was supposed to be the case even in v2, but as I said above: 'There ware bad decisions made, and supported'
 - Swappable routing: The route dispatcher is instantiated using a callback, which allows it to be changed with almost everything.
 - Semi-middleware (Value Middleware, i.e Decorators): These decorators, are more of value decorators, as their intent is to allow, developers to
 reuse common logic, like decoding a base64 encoded json string and returning the json array/object(it is a minimal example). [ WIP :construction: ]
 
## Installation
Just add `"codewave/codewave": "@stable"` to your composer.json required section and use `composer update` to install

## Documentation
Will follow shortly, just have to make the finishing touches and it will be added.
But if you are not patient enough to wait for stable, want to see for your self what is 
going on or you are interested in helping with the project, here is [the code to get you started](https://gist.github.com/DaGhostman/4217ca38261101a42864).


## Contributors
  - Dimitar Dimitrov a.k.a DaGhostman &lt;daghostman[at]gmail.com&gt;

### Stats
 [![Latest Stable Version](https://poser.pugx.org/codewave/codewave/v/stable.svg)](https://packagist.org/packages/codewave/codewave)
 
 [![Total Downloads](https://poser.pugx.org/codewave/codewave/downloads.svg)](https://packagist.org/packages/codewave/codewave) 
 
 [![Build Status](https://travis-ci.org/DaGhostman/codewave.svg?branch=master)](https://travis-ci.org/DaGhostman/codewave)
 
 [![Coverage Status](https://coveralls.io/repos/DaGhostman/codewave/badge.svg)](https://coveralls.io/r/DaGhostman/codewave)
 
 [![License](https://poser.pugx.org/codewave/codewave/license.svg)](https://packagist.org/packages/codewave/wavecode)


