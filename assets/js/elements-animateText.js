(function ($) {
    var getUniqueTarget = function (t) {
        var idTarget = t.data('id');
        var wrapAnimationText = '.elementor-element-' + idTarget;
        return wrapAnimationText;
    };
    var effect_1 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        for (var w = 0; w < words.length; w++) {
            target.find('.ml1 .letters-' + (w + 1)).each(function () {
                $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
            });
            //alert(w);
            sequenza
                    .add({
                        targets: getUniqueTarget(target) + ' .ml1 .letters-' + (w + 1) + ' .letter',
                        scale: [0.3, 1],
                        opacity: [0, 1],
                        translateZ: 0,
                        easing: "easeOutExpo",
                        duration: 600,
                        delay: function (el, i) {
                            return 70 * (i + 1)
                        }
                    }).add({
                targets: getUniqueTarget(target) + ' .ml1.w' + (w + 1) + ' .line',
                scaleX: [0, 1],
                opacity: [0.5, 1],
                easing: "easeOutExpo",
                duration: 700,
                offset: '-=875',
                delay: function (el, i, l) {
                    return 80 * (l - i);
                }
            }).add({
                targets: getUniqueTarget(target) + ' .ml1.w' + (w + 1),
                opacity: 0,
                duration: 1000,
                easing: "easeOutExpo",
                delay: 700
            });
        }
    };
    var effect_2 = function (target, words) {
        /*target.find('.ml2').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml2 .letter',
         scale: [4,1],
         opacity: [0,1],
         translateZ: 0,
         easing: "easeOutExpo",
         duration: 950,
         delay: function(el, i) {
         return 70*i;
         }
         }).add({
         targets: '.ml2',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        var sequenza = anime.timeline({
            loop: true
        });
        for (var w = 0; w < words.length; w++) {
            target.find('.ml2 .letters-' + (w + 1)).each(function () {
                $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
            });

            sequenza
                    .add({
                        targets: getUniqueTarget(target) + ' .ml2 .letters-' + (w + 1) + ' .letter',
                        scale: [4, 1],
                        opacity: [0, 1],
                        translateZ: 0,
                        easing: "easeOutExpo",
                        duration: 950,
                        delay: function (el, i) {
                            return 70 * i;
                        }
                    }).add({
                targets: getUniqueTarget(target) + ' .ml2.w' + (w + 1),
                opacity: 0,
                duration: 1000,
                easing: "easeOutExpo",
                delay: 1000
            });
        }
    };
    var effect_3 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });


        /*target.find('.ml3').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml3 .letter',
         opacity: [0,1],
         easing: "easeInOutQuad",
         duration: 2250,
         delay: function(el, i) {
         return 150 * (i+1)
         }
         }).add({
         targets: '.ml3',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/
        for (var w = 0; w < words.length; w++) {

            target.find('.ml3 .letters-' + (w + 1)).each(function () {
                $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
            });

            sequenza.timeline({loop: true})
                    .add({
                        targets: getUniqueTarget(target) + ' .ml3 .letter',
                        opacity: [0, 1],
                        easing: "easeInOutQuad",
                        duration: 2250,
                        delay: function (el, i) {
                            return 150 * (i + 1)
                        }
                    }).add({
                targets: getUniqueTarget(target) + ' .ml3',
                opacity: 0,
                duration: 1000,
                easing: "easeOutExpo",
                delay: 1000
            });
        }
    };
    var effect_4 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*var ml4 = {};
         ml4.opacityIn = [0,1];
         ml4.scaleIn = [0.2, 1];
         ml4.scaleOut = 3;
         ml4.durationIn = 800;
         ml4.durationOut = 600;
         ml4.delay = 500;
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml4 .letters-1',
         opacity: ml4.opacityIn,
         scale: ml4.scaleIn,
         duration: ml4.durationIn
         }).add({
         targets: '.ml4 .letters-1',
         opacity: 0,
         scale: ml4.scaleOut,
         duration: ml4.durationOut,
         easing: "easeInExpo",
         delay: ml4.delay
         }).add({
         targets: '.ml4 .letters-2',
         opacity: ml4.opacityIn,
         scale: ml4.scaleIn,
         duration: ml4.durationIn
         }).add({
         targets: '.ml4 .letters-2',
         opacity: 0,
         scale: ml4.scaleOut,
         duration: ml4.durationOut,
         easing: "easeInExpo",
         delay: ml4.delay
         }).add({
         targets: '.ml4 .letters-3',
         opacity: ml4.opacityIn,
         scale: ml4.scaleIn,
         duration: ml4.durationIn
         }).add({
         targets: '.ml4 .letters-3',
         opacity: 0,
         scale: ml4.scaleOut,
         duration: ml4.durationOut,
         easing: "easeInExpo",
         delay: ml4.delay
         }).add({
         targets: '.ml4',
         opacity: 0,
         duration: 500,
         delay: 500
         });*/

        var ml4 = {};
        ml4.opacityIn = [0, 1];
        ml4.scaleIn = [0.2, 1];
        ml4.scaleOut = 3;
        ml4.durationIn = 800;
        ml4.durationOut = 600;
        ml4.delay = 500;

        anime.timeline({loop: true})
                .add({
                    targets: '.ml4 .letters-1',
                    opacity: ml4.opacityIn,
                    scale: ml4.scaleIn,
                    duration: ml4.durationIn
                }).add({
            targets: '.ml4 .letters-1',
            opacity: 0,
            scale: ml4.scaleOut,
            duration: ml4.durationOut,
            easing: "easeInExpo",
            delay: ml4.delay
        }).add({
            targets: '.ml4 .letters-2',
            opacity: ml4.opacityIn,
            scale: ml4.scaleIn,
            duration: ml4.durationIn
        }).add({
            targets: '.ml4 .letters-2',
            opacity: 0,
            scale: ml4.scaleOut,
            duration: ml4.durationOut,
            easing: "easeInExpo",
            delay: ml4.delay
        }).add({
            targets: '.ml4 .letters-3',
            opacity: ml4.opacityIn,
            scale: ml4.scaleIn,
            duration: ml4.durationIn
        }).add({
            targets: '.ml4 .letters-3',
            opacity: 0,
            scale: ml4.scaleOut,
            duration: ml4.durationOut,
            easing: "easeInExpo",
            delay: ml4.delay
        }).add({
            targets: '.ml4',
            opacity: 0,
            duration: 500,
            delay: 500
        });
    };
    var effect_5 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*anime.timeline({loop: true})
         .add({
         targets: '.ml5 .line',
         opacity: [0.5,1],
         scaleX: [0, 1],
         easing: "easeInOutExpo",
         duration: 700
         }).add({
         targets: '.ml5 .line',
         duration: 600,
         easing: "easeOutExpo",
         translateY: function(e, i, l) {
         var offset = -0.625 + 0.625*2*i;
         return offset + "em";
         }
         }).add({
         targets: '.ml5 .ampersand',
         opacity: [0,1],
         scaleY: [0.5, 1],
         easing: "easeOutExpo",
         duration: 600,
         offset: '-=600'
         }).add({
         targets: '.ml5 .letters-left',
         opacity: [0,1],
         translateX: ["0.5em", 0],
         easing: "easeOutExpo",
         duration: 600,
         offset: '-=300'
         }).add({
         targets: '.ml5 .letters-right',
         opacity: [0,1],
         translateX: ["-0.5em", 0],
         easing: "easeOutExpo",
         duration: 600,
         offset: '-=600'
         }).add({
         targets: '.ml5',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        anime.timeline({loop: true})
                .add({
                    targets: '.ml5 .line',
                    opacity: [0.5, 1],
                    scaleX: [0, 1],
                    easing: "easeInOutExpo",
                    duration: 700
                }).add({
            targets: '.ml5 .line',
            duration: 600,
            easing: "easeOutExpo",
            translateY: function (e, i, l) {
                var offset = -0.625 + 0.625 * 2 * i;
                return offset + "em";
            }
        }).add({
            targets: '.ml5 .ampersand',
            opacity: [0, 1],
            scaleY: [0.5, 1],
            easing: "easeOutExpo",
            duration: 600,
            offset: '-=600'
        }).add({
            targets: '.ml5 .letters-left',
            opacity: [0, 1],
            translateX: ["0.5em", 0],
            easing: "easeOutExpo",
            duration: 600,
            offset: '-=300'
        }).add({
            targets: '.ml5 .letters-right',
            opacity: [0, 1],
            translateX: ["-0.5em", 0],
            easing: "easeOutExpo",
            duration: 600,
            offset: '-=600'
        }).add({
            targets: '.ml5',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };
    var effect_6 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml6 .letters').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml6 .letter',
         translateY: ["1.1em", 0],
         translateZ: 0,
         duration: 750,
         delay: function(el, i) {
         return 50 * i;
         }
         }).add({
         targets: '.ml6',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        target.find('.ml6 .letters').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml6 .letter',
                    translateY: ["1.1em", 0],
                    translateZ: 0,
                    duration: 750,
                    delay: function (el, i) {
                        return 50 * i;
                    }
                }).add({
            targets: '.ml6',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };
    var effect_7 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml7 .letters').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml7 .letter',
         translateY: ["1.1em", 0],
         translateX: ["0.55em", 0],
         translateZ: 0,
         rotateZ: [180, 0],
         duration: 750,
         easing: "easeOutExpo",
         delay: function(el, i) {
         return 50 * i;
         }
         }).add({
         targets: '.ml7',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        target.find('.ml7 .letters').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml7 .letter',
                    translateY: ["1.1em", 0],
                    translateX: ["0.55em", 0],
                    translateZ: 0,
                    rotateZ: [180, 0],
                    duration: 750,
                    easing: "easeOutExpo",
                    delay: function (el, i) {
                        return 50 * i;
                    }
                }).add({
            targets: '.ml7',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });


    };

    var effect_8 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*anime.timeline({loop: true})
         .add({
         targets: '.ml8 .circle-white',
         scale: [0, 3],
         opacity: [1, 0],
         easing: "easeInOutExpo",
         rotateZ: 360,
         duration: 1100
         }).add({
         targets: '.ml8 .circle-container',
         scale: [0, 1],
         duration: 1100,
         easing: "easeInOutExpo",
         offset: '-=1000'
         }).add({
         targets: '.ml8 .circle-dark',
         scale: [0, 1],
         duration: 1100,
         easing: "easeOutExpo",
         offset: '-=600'
         }).add({
         targets: '.ml8 .letters-left',
         scale: [0, 1],
         duration: 1200,
         offset: '-=550'
         }).add({
         targets: '.ml8 .bang',
         scale: [0, 1],
         rotateZ: [45, 15],
         duration: 1200,
         offset: '-=1000'
         }).add({
         targets: '.ml8',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1400
         });
         
         anime({
         targets: '.ml8 .circle-dark-dashed',
         rotateZ: 360,
         duration: 8000,
         easing: "linear",
         loop: true
         });*/

        anime.timeline({loop: true})
                .add({
                    targets: '.ml8 .circle-white',
                    scale: [0, 3],
                    opacity: [1, 0],
                    easing: "easeInOutExpo",
                    rotateZ: 360,
                    duration: 1100
                }).add({
            targets: '.ml8 .circle-container',
            scale: [0, 1],
            duration: 1100,
            easing: "easeInOutExpo",
            offset: '-=1000'
        }).add({
            targets: '.ml8 .circle-dark',
            scale: [0, 1],
            duration: 1100,
            easing: "easeOutExpo",
            offset: '-=600'
        }).add({
            targets: '.ml8 .letters-left',
            scale: [0, 1],
            duration: 1200,
            offset: '-=550'
        }).add({
            targets: '.ml8 .bang',
            scale: [0, 1],
            rotateZ: [45, 15],
            duration: 1200,
            offset: '-=1000'
        }).add({
            targets: '.ml8',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1400
        });

        anime({
            targets: '.ml8 .circle-dark-dashed',
            rotateZ: 360,
            duration: 8000,
            easing: "linear",
            loop: true
        });
    };
    var effect_9 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml9 .letters').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml9 .letter',
         scale: [0, 1],
         duration: 1500,
         elasticity: 600,
         delay: function(el, i) {
         return 45 * (i+1)
         }
         }).add({
         targets: '.ml9',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        target.find('.ml9 .letters').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml9 .letter',
                    scale: [0, 1],
                    duration: 1500,
                    elasticity: 600,
                    delay: function (el, i) {
                        return 45 * (i + 1)
                    }
                }).add({
            targets: '.ml9',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };
    var effect_10 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml10 .letters').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml10 .letter',
         rotateY: [-90, 0],
         duration: 1300,
         delay: function(el, i) {
         return 45 * i;
         }
         }).add({
         targets: '.ml10',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        target.find('.ml10 .letters').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml10 .letter',
                    rotateY: [-90, 0],
                    duration: 1300,
                    delay: function (el, i) {
                        return 45 * i;
                    }
                }).add({
            targets: '.ml10',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };
    var effect_11 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml11 .letters').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml11 .line',
         scaleY: [0,1],
         opacity: [0.5,1],
         easing: "easeOutExpo",
         duration: 700
         })
         .add({
         targets: '.ml11 .line',
         translateX: [0,$(".ml11 .letters").width()],
         easing: "easeOutExpo",
         duration: 700,
         delay: 100
         }).add({
         targets: '.ml11 .letter',
         opacity: [0,1],
         easing: "easeOutExpo",
         duration: 600,
         offset: '-=775',
         delay: function(el, i) {
         return 34 * (i+1)
         }
         }).add({
         targets: '.ml11',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        target.find('.ml11 .letters').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml11 .line',
                    scaleY: [0, 1],
                    opacity: [0.5, 1],
                    easing: "easeOutExpo",
                    duration: 700
                })
                .add({
                    targets: '.ml11 .line',
                    translateX: [0, $(".ml11 .letters").width()],
                    easing: "easeOutExpo",
                    duration: 700,
                    delay: 100
                }).add({
            targets: '.ml11 .letter',
            opacity: [0, 1],
            easing: "easeOutExpo",
            duration: 600,
            offset: '-=775',
            delay: function (el, i) {
                return 34 * (i + 1)
            }
        }).add({
            targets: '.ml11',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };
    var effect_12 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml12').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml12 .letter',
         translateX: [40,0],
         translateZ: 0,
         opacity: [0,1],
         easing: "easeOutExpo",
         duration: 1200,
         delay: function(el, i) {
         return 500 + 30 * i;
         }
         }).add({
         targets: '.ml12 .letter',
         translateX: [0,-30],
         opacity: [1,0],
         easing: "easeInExpo",
         duration: 1100,
         delay: function(el, i) {
         return 100 + 30 * i;
         }
         });*/

        target.find('.ml12').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml12 .letter',
                    translateX: [40, 0],
                    translateZ: 0,
                    opacity: [0, 1],
                    easing: "easeOutExpo",
                    duration: 1200,
                    delay: function (el, i) {
                        return 500 + 30 * i;
                    }
                }).add({
            targets: '.ml12 .letter',
            translateX: [0, -30],
            opacity: [1, 0],
            easing: "easeInExpo",
            duration: 1100,
            delay: function (el, i) {
                return 100 + 30 * i;
            }
        });
    };

    var effect_13 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });

        /*target.find('.ml13').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml13 .letter',
         translateY: [100,0],
         translateZ: 0,
         opacity: [0,1],
         easing: "easeOutExpo",
         duration: 1400,
         delay: function(el, i) {
         return 300 + 30 * i;
         }
         }).add({
         targets: '.ml13 .letter',
         translateY: [0,-100],
         opacity: [1,0],
         easing: "easeInExpo",
         duration: 1200,
         delay: function(el, i) {
         return 100 + 30 * i;
         }
         });*/

        target.find('.ml13').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml13 .letter',
                    translateY: [100, 0],
                    translateZ: 0,
                    opacity: [0, 1],
                    easing: "easeOutExpo",
                    duration: 1400,
                    delay: function (el, i) {
                        return 300 + 30 * i;
                    }
                }).add({
            targets: '.ml13 .letter',
            translateY: [0, -100],
            opacity: [1, 0],
            easing: "easeInExpo",
            duration: 1200,
            delay: function (el, i) {
                return 100 + 30 * i;
            }
        });
    };
    var effect_14 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml14 .letters').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml14 .line',
         scaleX: [0,1],
         opacity: [0.5,1],
         easing: "easeInOutExpo",
         duration: 900
         }).add({
         targets: '.ml14 .letter',
         opacity: [0,1],
         translateX: [40,0],
         translateZ: 0,
         scaleX: [0.3, 1],
         easing: "easeOutExpo",
         duration: 800,
         offset: '-=600',
         delay: function(el, i) {
         return 150 + 25 * i;
         }
         }).add({
         targets: '.ml14',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        target.find('.ml14 .letters').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml14 .line',
                    scaleX: [0, 1],
                    opacity: [0.5, 1],
                    easing: "easeInOutExpo",
                    duration: 900
                }).add({
            targets: '.ml14 .letter',
            opacity: [0, 1],
            translateX: [40, 0],
            translateZ: 0,
            scaleX: [0.3, 1],
            easing: "easeOutExpo",
            duration: 800,
            offset: '-=600',
            delay: function (el, i) {
                return 150 + 25 * i;
            }
        }).add({
            targets: '.ml14',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };
    var effect_15 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*anime.timeline({loop: true})
         .add({
         targets: '.ml15 .word',
         scale: [14,1],
         opacity: [0,1],
         easing: "easeOutCirc",
         duration: 800,
         delay: function(el, i) {
         return 800 * i;
         }
         }).add({
         targets: '.ml15',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        anime.timeline({loop: true})
                .add({
                    targets: '.ml15 .word',
                    scale: [14, 1],
                    opacity: [0, 1],
                    easing: "easeOutCirc",
                    duration: 800,
                    delay: function (el, i) {
                        return 800 * i;
                    }
                }).add({
            targets: '.ml15',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };
    var effect_16 = function (target, words) {
        var sequenza = anime.timeline({
            loop: true
        });
        /*target.find('.ml16').each(function(){
         $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
         });
         
         anime.timeline({loop: true})
         .add({
         targets: '.ml16 .letter',
         translateY: [-100,0],
         easing: "easeOutExpo",
         duration: 1400,
         delay: function(el, i) {
         return 30 * i;
         }
         }).add({
         targets: '.ml16',
         opacity: 0,
         duration: 1000,
         easing: "easeOutExpo",
         delay: 1000
         });*/

        target.find('.ml16').each(function () {
            $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
        });

        anime.timeline({loop: true})
                .add({
                    targets: '.ml16 .letter',
                    translateY: [-100, 0],
                    easing: "easeOutExpo",
                    duration: 1400,
                    delay: function (el, i) {
                        return 30 * i;
                    }
                }).add({
            targets: '.ml16',
            opacity: 0,
            duration: 1000,
            easing: "easeOutExpo",
            delay: 1000
        });
    };

    var WidgetElements_AnimateTextHandler = function ($scope, $) {
        //console.log( $scope );
        var elementSettings = get_Dyncontel_ElementSettings($scope);
        var eff = elementSettings.animate_effect;
        var target = $scope;
        var words = elementSettings.words;

        // -----------------------------
        if (eff == 1) {
            effect_1(target, words);
        } else if (eff == 2) {
            effect_2(target, words);
        } else if (eff == 3) {
            effect_3(target, words);
        } else if (eff == 4) {
            effect_4(target, words);
        } else if (eff == 5) {
            effect_5(target, words);
        } else if (eff == 6) {
            effect_6(target, words);
        } else if (eff == 7) {
            effect_7(target, words);
        } else if (eff == 8) {
            effect_8(target, words);
        } else if (eff == 9) {
            effect_9(target, words);
        } else if (eff == 10) {
            effect_10(target, words);
        } else if (eff == 11) {
            effect_11(target, words);
        } else if (eff == 12) {
            effect_12(target, words);
        } else if (eff == 13) {
            effect_13(target, words);
        } else if (eff == 14) {
            effect_14(target, words);
        } else if (eff == 15) {
            effect_15(target, words);
        } else if (eff == 16) {
            effect_16(target, words);
        }
        // -----------------------------

    };
    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        //alert('animate text');
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-animateText.default', WidgetElements_AnimateTextHandler);
    });
})(jQuery);
