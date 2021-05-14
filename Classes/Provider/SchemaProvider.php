<?php


namespace Wpu\Graphql\Provider;


use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class SchemaProvider implements SchemaProviderInterface
{
    public function createSchema(): Schema
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'echo' => [
                    'type' => Type::string(),
                    'args' => [
                        'message' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function ($rootValue, $args) {
//                        return ['prefix'] . $args['message'];
                        return "lol? ".$args['message'];
                    }
                ],
            ],
        ]);

        return new Schema([
            'query' => $queryType
        ]);
    }
}
