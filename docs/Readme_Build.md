# Documentation creation guide

## Prepare documentation

- Create your documentation directory (i.e. my_docs)
- Copy: _skel_files/mkdocs.yml, _skel_files/requirements.txt to my_docs/
- Copy: _skel_files/docs to my_docs/

## Installation

Install mkdocs

    pip install mkdocs

Install Material theme

    cd my_docs    
    pip install mkdocs-material

>NOTE: If something wrong, try: 

    pip install -r requirements.txt

## Customize/Write documentation

- Start editing the content of `./my_docs/mkdocs.yml`. Here you will edit the navigation content and others.

## Server local deve

  mkdocs serve --dev-addr 127.0.0.1:8081

## Build site

    mkdocs build 

Build with specified dir

    mkdocs build --site-dir ../web/docs/api

## References

- [Material theme documentation](https://squidfunk.github.io/mkdocs-material/reference/code-blocks/#docsstylesheetsextracss)
 