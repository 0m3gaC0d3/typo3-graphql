<?php


namespace Wpu\Graphql\Provider;


use GraphQL\Type\Schema;

interface SchemaProviderInterface
{
    public function createSchema(): Schema;
}
