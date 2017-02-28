# Deadly simple XPath-based parser

## Usage

### Parse element(-s) by single query
```php
$result = (new \eznio\xget\Xget(new \GuzzleHttp\Client()))
    ->setUrl('http://spb.questguild.ru/choose_city')
    ->parse([
        'cities' => '//ul[@class="list-check"]/li/a'
    ]);
    
$cities = ['Moscow', 'Saint-Petersburg', ... ];
```

### Parse elements with nested subqueries

A bit more difficult case.

First of all, you set root node to parse using `@` array key.

Then you define elements to be parsed from the root elements using XPath-queries from the root of the parent query result. 

```php
$result = (new \eznio\xget\Xget(new \GuzzleHttp\Client()))
    ->setUrl('http://spb.questguild.ru/choose_city')
    ->parse([
        'cities' => [
            '@' => '//ul[@class="list-check"]/li',
            'city' => '//a',
            'url' => '//a/@href'
        ]
    ]);

$cities = [
    [
        'name' => 'Moscow',
        'url' => 'http://moscow.questguild.ru',
    ],
    [
        'name' => 'Saint-Petersburg',
        'url' => 'http://spb.questguild.ru',
    ],
    . . .
];
```