<template>
    <div class="ships-info-component d-flex flex-column">
        <h5>Ship info:</h5>
        <div class="ship-info d-flex flex-column" v-for="item in numberOfParticularShips">
            <div class="ship-info-element-container d-flex"
                 v-for="elements in getNumberOfSpecifyShips(item)"
                 :ref="setRef">
                <div class="ship-info-element" v-for="element in getNumberOfSpecifyShipElement(item)"></div>
            </div>
        </div>
    </div>
</template>

<script>
import {shipsStorage} from "../../entities/game/ShipsStorage";
import {emitter} from "../../services/Emitter";

export default {
    name: 'ShipsInfoComponent',
    data() {
        return {
            'numberOfParticularShips': shipsStorage.numberOfParticularShips,
            'elements': [],
        };
    },
    props: ['isUserOwner'],
    beforeUpdate() {
        this.elements = [];
    },
    mounted() {
        emitter.on('updateShipInfo', shipInfo => {
            if (shipInfo.isUserOwner !== this.isUserOwner) {
                return;
            }
            
            this.elements[shipInfo.shipId].querySelectorAll('.ship-info-element').forEach(element => {
                element.classList.add('killed');
            });
        });
    },
    methods: {
        getNumberOfSpecifyShips(numberOfParticularShip) {
            return shipsStorage.getNumberOfSpecifyShips(numberOfParticularShip);
        },
        getNumberOfSpecifyShipElement(numberOfParticularShip) {
            return shipsStorage.getNumberOfSpecifyShipElement(numberOfParticularShip);
        },
        setRef(element) {
            if (element) {
                this.elements.push(element);
            }
        },
    }
};
</script>