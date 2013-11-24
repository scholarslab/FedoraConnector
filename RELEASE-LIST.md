
# Release Checklist

It's easiest to start with a fresh repository, so the instructions start there.

1. `VERSION=42.0.13` — We'll use this value later.
1. `git clone git://github.com/omeka/Omeka.git` — We need Omeka for generating
  translations.
1. `cd omeka/plugins`
1. `git clone git@github.com:scholarslab/FedoraConnector.git`
1. `cd FedoraConnector`
1. `git flow init`
1. `git flow release start $VERSION`
1. `rake version[$VERSION]`
1. `git commit`
1. `rake package`
1. quick check the zip
1. test the zip
1. `git flow release finish $VERSION`
1. `git push`
1. `git push --tags`
1. upload the zip to http://omeka.org/add-ons/plugins/.
