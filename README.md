# Doctrine Criteria Serializer Bundle

[![Build Status](https://scrutinizer-ci.com/g/mikemirten/DoctrineCriteriaSerializer-Bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mikemirten/DoctrineCriteriaSerializer-Bundle/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/mikemirten/DoctrineCriteriaSerializer-Bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mikemirten/DoctrineCriteriaSerializer-Bundle/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mikemirten/DoctrineCriteriaSerializer-Bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mikemirten/DoctrineCriteriaSerializer-Bundle/?branch=master)

A [bundle](https://symfony.com/doc/current/bundles.html) of [Symfony Framework](https://symfony.com/) provides integration with the [Doctrine Criteria Serializer](https://github.com/mikemirten/DoctrineCriteriaSerializer) component.

## Requirements

**PHP** 7.1 or later.

**Symfony Framework** 3.0 or later.

## Installation

Through composer:
```composer require mikemirten/doctrine-criteria-serializer-bundle```

Register bundle in the Kernel:
```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\DoctrineCriteriaSerializerBundle(),
            // ...
        ];
    }
}
```

## Criteria parameter converter

The bundle provides a [parameter converter](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html) to unserialize HTTP query into an instance of [Criteria](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/working-with-associations.html#filtering-collections).

```php
namespace AppBundle\Controller;

use Doctrine\Common\Collections\Criteria

class AuthorController
{
    /**
     * Repository of authors
     *
     * @var AuthorRepository $authorRepository
     */
    protected $authorRepository;

    /**
     * Constructor
     *
     * @param AuthorRepository $authorRepository
     */
    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }
    
    /**
     * Find authors
     *
     * @param  Criteria $criteria
     * @return Response
     */
    public function find(Criteria $criteria): Response
    {
        $authors = $this->authorRepository->matching($criteria);
        
        // ...
    }

    /**
     * Find books of given author
     *
     * @param  Author   $author
     * @param  Criteria $criteria
     * @return Response
     */
    public function findBooks(Author $author, Criteria $criteria): Response
    {
        $books = $author->getBooks()->matching($criteria);
        
        // ...
    }
}
```
