<?php

namespace App\Http\Controllers;

use App\Post;
use App\Tag;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DemoController extends Controller
{
    /**
     * Add tags from an existing list or create new ones
     * Functions: map(), all()
     */
    public function taggingWithoutCollection()
    {
        //Example Data
        $data = [
            'post_id' => 1,
            'tags'    => [
                17,
                32,
                'recipes',
                11,
                'kitchen'
            ]
        ];

        $post = Post::find($data['post_id']);
        $tagIds = [];
        foreach (request('tags') as $nameOrId) {
            if (is_numeric($nameOrId)) {
                $tagIds[] = $nameOrId;
            } else {
                $tag = Tag::create(['name' => $nameOrId]);
                $tagIds[] = $tag->id;
            }
        }
        $post->tags()->sync($tagIds);

        return view('posts.show', compact('post'));
    }

    public function tagging(Request $request)
    {
        $post = Post::find($request->post_id);
        $tagIds = $this->normalizeTagsToIds(request('tags'));
        $post->tags()->sync($tagIds);

        return view('posts.index');
    }

    private function normalizeTagsToIds($tags)
    {
        return collect($tags)->map(function ($nameOrId) {
            if (is_numeric($nameOrId)) {
                return $nameOrId;
            }

            return Tag::create(['name' => $nameOrId])->id;
        })->all();
    }

    /**
     * Given a JSON feed of products from a store, figure out how much it would cost
     * to buy every variant of every single lamp and wallet that store has for sale
     * Functions: filter(), contains(), flatMap(), map(), sum()
     */
    public function pricingLampsAndWallet()
    {
        $json = '{
            "products": [
                {
                    "title": "Small Rubber Wallet",
                    "product_type": "Wallet",
                    "variants": [
                        {
                            "title": "Blue",
                            "price": 29.33
                        },
                        {
                            "title": "Turquoise",
                            "price": 18.5
                        }
                    ]
                },
                {
                    "title": "Sleek Cotton Shoes",
                    "product_type": "Shoes",
                    "variants": [
                        {
                            "title": "Sky Blue",
                            "price": 20
                        }
                    ]
                },
                {
                    "title": "Intelligent Cotton Wallet",
                    "product_type": "Wallet",
                    "variants": [
                        {
                            "title": "White",
                            "price": 17.97
                        }
                    ]
                },
                {
                    "title": "Enormous Leather Lamp",
                    "product_type": "Lamp",
                    "variants": [
                        {
                            "title": "Azure",
                            "price": 65.99
                        },
                        {
                            "title": "Salmon",
                            "price": 1.66
                        }
                    ]
                }
            ]
        }';
        $productJson = json_decode($json, true);
        $products = collect($productJson['products']);

        return $products->filter(function ($product) {
            return collect(['Lamp', 'Wallet'])->contains($product['product_type']);
        })->flatMap(function ($product) {
            return $product['variants'];
        })->sum('price');
    }

    /**
     * Pricing Products without collection
     */
    public function pricingWithoutCollection($products)
    {
        $totalCost = 0;
        // Loop over every product
        foreach ($products as $product) {
            $productType = $product['product_type'];
            // If the product is a lamp or wallet...
            if ($productType == 'Lamp' || $productType == 'Wallet') {
                // Loop over the variants and add up their prices
                foreach ($product['variants'] as $productVariant) {
                    $totalCost += $productVariant['price'];
                }
            }
        }

        return $totalCost;
    }


    /**
     * Binary to decimal
     * Functions: reverse(), values(), map()
     * Example of decimal:  3716 = o (3 x 103) + (7 x 102) + (1 x 101) + (6 x 100)
     * Example of binary to decimal: 11010 =  (1 x 24) + (1 x 23) + (0 x 22) + (1 x 21) + (0 x 20) => 26 Decimal
     */
    public function binaryToDecimal()
    {
        $binary = 11010;

//        return $this->binaryToDecimalWithoutCollection($binary);
        return collect(str_split($binary))
            ->reverse()
            ->values()
            ->map(function ($column, $exponent) {
                return $column * (2 ** $exponent);
            })
            ->sum();
    }


    public function binaryToDecimalWithoutCollection($binary)
    {
        $total = 0;
        //exponent: số mũ
        $exponent = strlen($binary) - 1;
        $binary = str_split($binary);
        for ($i = 0; $i < strlen($binary); $i++) {
            $decimal = $binary[$i] * (2 ** $exponent);
            $total += $decimal;
            $exponent--;
        }

        return $total;
    }

    /**
     * Generate report compare revenue every months this year to revenue from every month last year
     *
     */
    public function compareMonthlyRevenue()
    {
        $lastYear = [
            2976.50, // Jan
            2788.84, // Feb
            2353.92, // Mar
            3365.36, // Apr
            2532.99, // May
            1598.42, // Jun
            2751.82, // Jul
            2576.17, // Aug
            2324.87, // Sep
            2299.21, // Oct
            3483.10, // Nov
            2245.08, // Dec
        ];
        $thisYear = [
            3461.77,
            3665.17,
            3210.53,
            3529.07,
            3376.66,
            3825.49,
            2165.24,
            2261.40,
            3988.76,
            3302.42,
            3345.41,
            2904.80
        ];

        return $this->compareRevenueWithoutCollection($lastYear, $thisYear);
        //zip() usually use when need loop over 2 arrays at once
//        return collect($thisYear)->zip($lastYear)->map(function ($thisAndLast) {
//            return $thisAndLast[0] - $thisAndLast[1];
//        });
    }

    public function compareRevenueWithoutCollection($lastYear, $thisYear)
    {
        $deltas = [];
        foreach ($lastYear as $month => $monthlyRevenue) {
            $deltas[] = $thisYear[$month] - $monthlyRevenue;
        }

        return $deltas;
    }


    /**
     * Ranking Kcoin
     * Functions: zip(), map(), groupBy(), collapse(), sortBy(), sortByDesc(), values(), pipe()
     * @return \Illuminate\Support\Collection
     */
    public function rankingKcoin()
    {
        $scores = collect([
            ['name' => "KidsOnline 1", 'score' => 91],
            ['name' => "KidsOnline 2", 'score' => 76],
            ['name' => "KidsOnline 3", 'score' => 82],
            ['name' => "KidsOnline 4", 'score' => 86],
            ['name' => "KidsOnline 5", 'score' => 99],
            ['name' => "KidsOnline 6", 'score' => 67],
            ['name' => "KidsOnline 7", 'score' => 67],
            ['name' => "KidsOnline 8", 'score' => 76],
            ['name' => "KidsOnline 9", 'score' => 82],
            ['name' => "KidsOnline 10", 'score' => 94],
        ]);

        $rankedScores = $scores->sortByDesc('score')
            //Fix index when sorting
            ->values()
            //Zip score with list of rank
            ->zip(range(1, $scores->count()))
            //Map rank to field in each score
            ->map(function ($scoreAndRank) {
                list($score, $rank) = $scoreAndRank;

                return array_merge($score, ['rank' => $rank]);
            })
            //Group score to dealing with tied score
            ->groupBy('score')
            //Map rank of tied scores with its min rank
            ->map(function ($tiedScores) {
                //Get min rank
                $lowestRank = collect($tiedScores)->pluck('rank')->min();

                return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
                    return array_merge($rankedScore, ['rank' => $lowestRank]);
                });
            })
            //Flatten collections of scores
            ->collapse()
            //Sort
            ->sortBy('rank');

        return $rankedScores;

//        return $this->rankingKcoinWithPipeMethod($scores);
    }

    public function rankingKcoinWithPipeMethod($scores)
    {
        return collect($scores)
            ->pipe(function ($scores) {
                return $this->assignInitialRankings($scores);
            })
            ->pipe(function ($scores) {
                return $this->adjustRankingsForTies($scores);
            })
            ->sortBy('rank');
    }

    public function assignInitialRankings($scores)
    {
        return $scores->sortByDesc('score')
            ->zip(range(1, $scores->count()))
            ->map(function ($scoreAndRank) {
                list($score, $rank) = $scoreAndRank;

                return array_merge($score, ['rank' => $rank]);
            });
    }

    public function adjustRankingsForTies($scores)
    {
        return $scores->groupBy('score')->map(function ($tiedScores) {
            return $this->applyMinRank($tiedScores);
        })->collapse();
    }

    public function applyMinRank($tiedScores)
    {
        $lowestRank = $tiedScores->pluck('rank')->min();

        return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
            return array_merge($rankedScore, ['rank' => $lowestRank]);
        });
    }
}
