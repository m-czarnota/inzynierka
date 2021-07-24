<template>

</template>

<script>
export default {
    name: "ShipPoses",
    data() {
        return {
            poses4Elements: [
                [
                    /*
                    +
                    + +
                      +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    },
                ],
                [
                    /*
                      +
                    + +
                    +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    },
                ],
                [
                    /*
                    + +
                      + +
                    */
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    },
                ],
                [
                    /*
                      + +
                    + +
                    */
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 1,
                    },
                ],
                [
                    /*
                    + + + +
                    */
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 4,
                    },
                ],
                [
                    /*
                    +
                    +
                    +
                    +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 4,
                        'grid-column-start': 2,
                    },
                ],
                [
                    /*
                    + + +
                        +
                    */
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    }
                ],
                [
                    /*
                    + + +
                    +
                    */
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    }
                ],
                [
                    /*
                    +
                    + + +
                    */
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    }
                ],
                [
                    /*
                        +
                    + + +
                    */
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 1,
                    }
                ],
                [
                    /*
                    + +
                    +
                    +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    }
                ],
                [
                    /*
                    +
                    +
                    + +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    }
                ],
                [
                    /*
                    + +
                      +
                      +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    },
                ],
                [
                    /*
                      +
                      +
                    + +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 3,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    }
                ]
            ],
            poses3Elements: [
                [
                    /*
                    + +
                      +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    }
                ],
                [
                    /*
                    + +
                    +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    }
                ],
                [
                    /*
                    +
                    + +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    }
                ],
                [
                    /*
                      +
                    + +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    }
                ],
                [
                    /*
                    + + +
                    */
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 3,
                    }
                ],
                [
                    /*
                    +
                    +
                    +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 2,
                    },
                    {
                        'grid-row-start': 3,
                        'grid-column-start': 2,
                    }
                ]
            ],
            poses2Elements: [
                [
                    /*
                    + +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 2,
                    }
                ],
                [
                    /*
                    +
                    +
                    */
                    {
                        'grid-row-start': 1,
                        'grid-column-start': 1,
                    },
                    {
                        'grid-row-start': 2,
                        'grid-column-start': 1,
                    }
                ]
            ]
        }
    }
}
</script>

<style scoped>

</style>