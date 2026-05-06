window.feed = function (callback) {
  var tick = {};
  tick.plot0 = Math.ceil(0 + Math.random() * 1000000);
  callback(JSON.stringify(tick));
};

var myConfig = {
  type: "gauge",
  globals: {
    fontSize: 25,
  },
  plotarea: {
    marginTop: 100,
  },
  plot: {
    size: "100%",
    valueBox: {
      placement: "center",
      text: "%v",
      fontSize: 25,
      rules: [
        {
          rule: "%v == 1000000",
          text: "%v <br> Target achieved!",
        },
        {
          rule: "%v > 750000 && %v < 1000000",
          text: "%v <br> Way to go!",
        },
        {
          rule: "%v > 500000  && %v <= 750000",
          text: "%v <br> Almost there!",
        },
        {
          rule: "%v >= 250000 && %v <= 500000",
          text: "%v <br> Keep it up!",
        },
        {
          rule: "%v < 250000",
          text: "%v <br> We just started!",
        },
      ],
    },
  },
  tooltip: {
    borderRadius: 5,
  },
  scaleR: {
    aperture: 180,
    minValue: 0,
    maxValue: 1000000,
    step: 250000,
    center: {
      visible: false,
    },
    tick: {
      visible: false,
    },
    item: {
      offsetR: 0,
      rules: [
        {
          rule: "%i == 9",
          offsetX: 15,
        },
      ],
    },
    labels: ["0", "250.000", "500.000", "750.000", "1.000.000"],
    ring: {
      size: 50,
      rules: [
        {
          rule: "%v < 250000",
          backgroundColor: "#E53935",
        },
        {
          rule: "%v >= 250000 && %v < 500000",
          backgroundColor: "#FFFF00",
        },
        {
          rule: "%v >= 500000 && %v < 750000",
          backgroundColor: "#3CB371",
        },
        {
          rule: "%v >= 750000",
          backgroundColor: "#29B6F6",
        },
      ],
    },
  },
  refresh: {
    type: "feed",
    transport: "js",
    url: "feed()",
    interval: 1500,
    resetTimeout: 1000,
  },
  series: [
    {
      values: [0], // starting value
      backgroundColor: "black",
      indicator: [10, 5, 5, 5, 0.75],
      animation: {
        effect: 2,
        method: 1,
        sequence: 4,
        speed: 900,
      },
    },
  ],
};

zingchart.render({
  id: "myChart",
  data: myConfig,
  height: 500,
  width: "100%",
});
