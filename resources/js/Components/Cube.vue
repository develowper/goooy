<template>
  <div class=" h-72    overflow-hidden">

    <div class="scene  " id="scene">
      <div class="pivot centered" id="pivot" style="transform: rotateX(-20deg) rotateY(225deg)">
        <div v-if="true" class="cube " id="cube">
          <div v-for="(piece,idx) in pieces" class="piece " :id="`${piece}${idx+1}`"
               style="transform: rotateX(0deg) translateX(-2em);">
            <div v-for="face in faces" :class="`element ${face}`">

            </div>

          </div>
        </div>
        <div v-if="false" class="cube" id="cube">
          <div class="piece" id="piece1" style="transform: rotateX(0deg) translateX(-2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece2" style="transform: rotateX(0deg) translateX(2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece4" style="transform: rotateX(0deg) translateY(-2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece8" style="transform: rotateX(0deg) translateY(2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece16" style="transform: rotateX(0deg) translateZ(-2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece32" style="transform: rotateX(0deg) translateZ(2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece9" style="transform: rotateX(0deg) translateX(-2em) translateY(2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece6" style="transform: rotateX(0deg) translateX(2em) translateY(-2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece20" style="transform: rotateX(0deg) translateY(-2em) translateZ(-2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece40" style="transform: rotateX(0deg) translateY(2em) translateZ(2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece18" style="transform: rotateX(0deg) translateZ(-2em) translateX(2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece33" style="transform: rotateX(0deg) translateZ(2em) translateX(-2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece5" style="transform: rotateX(0deg) translateX(-2em) translateY(-2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece10" style="transform: rotateX(0deg) translateX(2em) translateY(2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back"></div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece36" style="transform: rotateX(0deg) translateY(-2em) translateZ(2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece24" style="transform: rotateX(0deg) translateY(2em) translateZ(-2em);">
            <div class="element left"></div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece17" style="transform: rotateX(0deg) translateZ(-2em) translateX(-2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece34" style="transform: rotateX(0deg) translateZ(2em) translateX(2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top"></div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece21"
               style="transform: rotateX(0deg) translateX(-2em) translateY(-2em) translateZ(-2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece42"
               style="transform: rotateX(0deg) translateX(2em) translateY(2em) translateZ(2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece37"
               style="transform: rotateX(0deg) translateY(-2em) translateZ(2em) translateX(-2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece26"
               style="transform: rotateX(0deg) translateY(2em) translateZ(-2em) translateX(2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece25"
               style="transform: rotateX(0deg) translateZ(-2em) translateX(-2em) translateY(2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
          <div class="piece" id="piece38"
               style="transform: rotateX(0deg) translateZ(2em) translateX(2em) translateY(-2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece41"
               style="transform: rotateX(0deg) translateX(-2em) translateY(2em) translateZ(2em);">
            <div class="element left">
              <div class="text-gray-900 flex items-center justify-center sticker blue">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه0
                </div>
              </div>
            </div>
            <div class="element right"></div>
            <div class="element top"></div>
            <div class="element bottom">
              <div class="text-gray-900 flex items-center justify-center sticker yellow">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(180deg);">فروشگاه3
                </div>
              </div>
            </div>
            <div class="element back"></div>
            <div class="element front">
              <div class="text-gray-900 flex items-center justify-center sticker red">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه5</div>
              </div>
            </div>
          </div>
          <div class="piece" id="piece22"
               style="transform: rotateX(0deg) translateX(2em) translateY(-2em) translateZ(-2em);">
            <div class="element left"></div>
            <div class="element right">
              <div class="text-gray-900 flex items-center justify-center sticker green">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(-90deg);">فروشگاه1
                </div>
              </div>
            </div>
            <div class="element top">
              <div class="text-gray-900 flex items-center justify-center sticker white">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(0deg);">فروشگاه2</div>
              </div>
            </div>
            <div class="element bottom"></div>
            <div class="element back">
              <div class="text-gray-900 flex items-center justify-center sticker orange">
                <div class="select-none text-center " style="font-size: 12px; transform: rotateZ(90deg);">فروشگاه4</div>
              </div>
            </div>
            <div class="element front"></div>
          </div>
        </div>
      </div>
      <div v-if="false" class="credits">
        <div class="text" style="position: initial">Click and then drag the edge</div>
      </div>
    </div>


    <div id="guide">
      <div class="anchor " id="anchor3"
           style="transform: translateZ(3px) translateY(-33.33%) rotate(-270deg) translateY(66.67%)"></div>
      <div class="anchor" id="anchor2"
           style="transform: translateZ(3px) translateY(-33.33%) rotate(-180deg) translateY(66.67%)"></div>
      <div class="anchor" id="anchor1"
           style="transform: translateZ(3px) translateY(-33.33%) rotate(-90deg) translateY(66.67%)"></div>
      <div class="anchor" id="anchor0"
           style="transform: translateZ(3px) translateY(-33.33%) rotate(0deg) translateY(66.67%)"></div>
    </div>
  </div>

</template>

<script>

var colors = ["blue", "green", "white", "yellow", "orange", "red"],
    pieces = document.getElementsByClassName("piece"),
    scene = document.getElementById("scene"),
    index = 1,
    faceCode = {8: 0, 1: 1, 7: 2, 2: 3, 0: 4, 4: 5, 6: 6, 3: 7, 5: 8},
    map = {
      47: 0, 9: 1, 52: 2, 30: 3, 2: 4, 16: 5, 34: 6, 21: 7, 42: 8,
      54: 9, 12: 10, 33: 11, 15: 12, 5: 13, 27: 14, 41: 15, 26: 16, 43: 17,
      48: 18, 23: 19, 37: 20, 10: 21, 3: 22, 20: 23, 53: 24, 11: 25, 32: 26,
      31: 27, 19: 28, 39: 29, 28: 30, 1: 31, 18: 32, 44: 33, 7: 34, 49: 35,
      38: 36, 24: 37, 46: 38, 17: 39, 6: 40, 29: 41, 51: 42, 14: 43, 36: 44,
      35: 45, 22: 46, 40: 47, 13: 48, 4: 49, 25: 50, 50: 51, 8: 52, 45: 53,
    };
let self;
export default {
  name: "Cube",
  props: ['data'],
  data() {
    return {
      pieces: 26,
      faces: ['left', 'right', 'top', 'bottom', 'back', 'front'],
      // colors: ["blue", "green", "white", "yellow", "orange", "red"],
      colors: ["blue", "green", "white", "yellow", "orange", "red"],
    }
  },
  mounted() {
    index = 1;
    pieces = document.getElementsByClassName("piece");
    scene = document.getElementById("scene");

    self = this;
    document.ondragstart = function () {
      return !1
    };
    scene.addEventListener("touchstart", function () {
      return !1
    }, {passive: false});
    this.ass();
    // window.addEventListener("load", this.ass);
    scene.addEventListener("mousedown", this.md);
    scene.addEventListener("touchstart", this.md, {passive: false});
    // this.$forceUpdate();
    // this.$nextTick(() => {
    // });
  },

  updated() {
  },
  methods: {

    mx(e, t) {
      return ([2, 4, 3, 5][t % 4 | 0] + e % 2 * ((0 | t) % 4 * 2 + 3) + 2 * (e / 2 | 0)) % 6
    }
    ,
    ga(e) {
      return String.fromCharCode("X".charCodeAt(0) + e / 2)
    }
    ,
    ass() {

      function e(e) {
        let idx = index++;
        let item = self.data[map[idx]];
        let face = document.createElement("a");
        face.classList += 'text-gray-900 p-1 flex items-center justify-center' + " sticker " + colors[e];
        face.setAttribute('href', item.link);
        face.setAttribute('target', 'new');
        var text = document.createElement("div");
        if (item.icon) {
          text = document.createElement("img");
          text.style.backgroundImage = item.icon;
          text.setAttribute('src', item.icon);
        } else {
          text.classList = "select-none text-center ";
          text.style.fontSize = "12px";
          text.innerHTML = (item.title);
        }
        // text.setAttribute('href', 'https://ailverchi.ir/shop');

        let rotate = 0;
        switch (e) {
          case 0: //blue
            rotate = 180;
            break;
          case 1: //green
            rotate = -90;
            break;
          case 2: //white
            break;
          case 3: //yellow
            rotate = 180;
            break;
          case 4: //orange
            rotate = 90;
            break;
          case 5: //red
            break;
        }
        text.style.transform = `rotateZ(${rotate}deg)`;
        face.appendChild(text);
        self.$forceUpdate();
        self.$nextTick(() => {

        });
        // face.addEventListener('click', function (e) {
        //       e.preventDefault();
        //       window.location = 'https://ailverchi.ir/shop';
        //     }, {passive: false}
        // );
        // face.addEventListener('touchstart', function (e) {
        //       e.preventDefault();
        //       window.location = 'https://ailverchi.ir/shop';
        //     }, {passive: false}
        // )

        return t += 1 << e, pieces[o].children[e].appendChild(face), "translate" + self.ga(e) + "(" + (e % 2 * 4 - 2) + "em)"
      }


      for (var t, n, o = 0; t = 0, o < 26; o++) {

        n = this.mx(o, o % 18), pieces[o].style.transform = "rotateX(0deg)" + e(o % 6) + (o > 5 ? e(n) + (o > 17 ? e(this.mx(n, n + 2)) : "") : ""), pieces[o].setAttribute("id", "piece" + t);
        // for (let i = 0; i++ < 9;) setTimeout(this.ani, 999 * i, Math.random() * 10 % 6 | 0, i % 2)
      }
    }

    ,
    gp(e, t, n) {
      return document.getElementById("piece" + ((1 << e) + (1 << this.mx(e, t)) + (1 << this.mx(e, t + 1)) * n))
    }
    ,
    swap(e, t) {
      for (var n = 0; n < 6 * t; n++) for (var o = this.gp(e, n / 2, n % 2), r = this.gp(e, n / 2 + 1, n % 2), a = 0; a < 5; a++) {
        var i = o.children[a < 4 ? this.mx(e, a) : e].firstChild,
            s = r.children[a < 4 ? this.mx(e, a + 1) : e].firstChild,
            c = i ? i.className : "";
        c && (i.className = s.className, s.className = c)
      }
    }
    ,

    ani(e, t, n = Date.now()) {
      var o = .3 * (e % 2 * 2 - 1) * (2 * t - 1), r = Array(9).fill(pieces[e]).map(function (t, n) {
        return n ? self.gp(e, n / 2, n % 2) : t
      });
      !function a() {
        var i = Date.now() - n, s = "rotate" + self.ga(e) + "(" + o * i * (i < 300) + "deg)";
        if (r.forEach(function (e) {
          e.style.transform = e.style.transform.replace(/rotate.\(\S+\)/, s)
        }), i >= 300) return self.swap(e, 3 - 2 * t);
        requestAnimationFrame(a)
      }()
    }
    ,
    md(e) {

      var t = pivot.style.transform.match(/-?\d+\.?\d*/g).map(Number), n = e.target.closest(".element"),
          o = [].indexOf.call((n || cube).parentNode.children, n);

      function r(r) {
        if (r.touches && r.touches[0].pageY) {
          r.pageY = r.touches[0].pageY;
          r.pageX = r.touches[0].pageX;
        }
        if (e.touches && e.touches[0].pageY) {
          e.pageY = e.touches[0].pageY;
          e.pageX = e.touches[0].pageX;
        }
        // if (n) {
        //   var i = /\d/.exec(document.elementFromPoint(r.pageX, r.pageY).id);
        //   if (i && i.input.includes("anchor")) {
        //     a();
        //     var s = n.parentNode.children[self.mx(o, Number(i) + 3)].hasChildNodes();
        //     self.ani(self.mx(o, Number(i) + 1 + 2 * s), s)
        //   }
        // } else
        pivot.style.transform = "rotateX(" + (t[0] - (r.pageY - e.pageY) / 2) + "deg)rotateY(" + (t[1] + (r.pageX - e.pageX) / 2) + "deg)"
      }

      function a() {
        document.body.appendChild(guide), scene.removeEventListener("mousemove", r), document.removeEventListener("mouseup", a), scene.addEventListener("mousedown", self.md);
      }

      function b() {
        scene.removeEventListener("touchmove", r), document.removeEventListener("touchend", a), scene.addEventListener("touchstart", self.md, {passive: true});
      }

      (n || document.body).appendChild(guide), scene.addEventListener("mousemove", r), document.addEventListener("mouseup", a), scene.removeEventListener("mousedown", self.md);
      scene.addEventListener("touchmove", r, {passive: true}), document.addEventListener("touchend", b, {passive: true}), scene.removeEventListener("touchstart", self.md);
    }

  },
}


</script>

<style lang="scss">
$base-color: #1e1f26;
$ground-color: #248C46;
$element-size: 2em;
$sticker-size: 93%;
$rounded: 10%;
$cube-scale: 2;
$faces: (left: (0, -90, 180), right: (0, 90, 90), back: (0, 180, -90), front: (0, 0, 0), bottom: (-90, 0, -90), top: (90, 0, 180));
//$colors: (blue: #006FB2, green: #1AC54C, white: #F9F7EC, yellow: #FFCF36, orange: #FF5721, red: #FF0030);
$colors: (blue: #e7ded5, green: #e7ded5, white: #e7ded5, yellow: #e7ded5, orange: #e7ded5, red: #e7ded5);

html, body {
  height: 100vh;
  min-height: -webkit-fill-available;
  //overflow: hidden;
  //background: $ground-color;
}


.centered {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  margin: auto;
}

.credits {
  margin: 2rem;
}

.text {
  font: {
    family: Arial;
    size: 1.2rem;
  }
  color: #F9F7EC;
  letter-spacing: .02em;
  background-color: #d9b200;
  padding: .2em .5em;
  border-radius: 0.25em;
  pointer-events: none;
}

.scene {
  height: 100%;
  width: 100%;
  perspective: 1200px;
  transform-style: preserve-3d;
  color: #ffdb14;

  > .pivot {
    width: 0;
    height: 0;
    transition: .2s;
  }

  .anchor {
    width: $element-size;
    height: $element-size * 3;
  }

  div {
    position: absolute;
    transform-style: inherit;
  }
}

.cube {
  font-size: $cube-scale * 100%;
  margin-left: calc(-1 * $element-size / 2);
  margin-top: calc(-1 * $element-size / 2);

  > .piece {
    width: $element-size - .1em;
    height: $element-size - .1em;

    > .element {
      width: 100%;
      height: 100%;
      background: $base-color;
      outline: 1px solid transparent; // firefox aliasing
      border: .05em solid $base-color {
        radius: $rounded
      }
      @each $face, $angles in $faces {
        &.#{""+$face} {
          transform: rotateX + '(' + nth($angles, 1) + 'deg)' rotateY + '(' + nth($angles, 2) + 'deg)' rotateZ + '(' + nth($angles, 3) + 'deg)' translateZ(calc($element-size / 2));
        }
      }

      > .sticker {
        @extend .centered;
        transform: translateZ(2px);
        width: $sticker-size;
        height: $sticker-size;
        border-radius: $rounded;
        outline: 1px solid transparent; // firefox aliasing
        @each $color, $value in $colors {
          &.#{""+$color} {
            background-color: $value;

            &:hover {
              background-color: rgba(250, 250, 250, .9);
              cursor: pointer;
            }
          }
        }
      }
    }
  }

}
</style>