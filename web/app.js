var apiURL = '/api/v1/'
var conversationResource = 'conversations'
var verifyResource = 'verify'

var conversations = new Vue({
	el: '#conversations',

	data: {
		convos: null,
		translated: [],
		results: [],
		currentSentence: 0,
		incorrect: [],
		completed: [],
		attemptsRemaining: 3,
		failed: false,
		audioConvo: null
	},

	created: function () {
		this.fetchData()
		this.audioConvo = new Audio('/audio/1_1.mp3')
		this.audioConvo.currentTime = 10
		// this.audioConvo.play()
	},

	methods: {
		fetchData: function () {
			var xhr = new XMLHttpRequest()
			var self = this
			xhr.open('GET', apiURL + conversationResource)
			xhr.onload = function () {
				self.convos = JSON.parse(xhr.responseText)
			}
			xhr.send()
		},

		checkText: function (translation, sentence_num, sequence, part) {
      original_spanish = this.getSentenceText('es', sequence, part)

      var dmp = new diff_match_patch()

      // no timeout
      dmp.Diff_Timeout = 0

      var d = dmp.diff_main(translation, original_spanish.conversation[sentence_num].text)

      dmp.diff_cleanupSemantic(d)

      Vue.set(this.results, sentence_num, dmp.diff_prettyHtml(d))

      // if the guess is correct
      if (d.length == 1 && d[0][0] == 0) {
      	this.currentSentence ++
      	Vue.set(this.completed, sentence_num, true)
      }
      else {
      	Vue.set(this.incorrect, sentence_num, true)

      	this.attemptsRemaining--

      	if (this.attemptsRemaining < 1) {
      		this.failed = true
      	}
      }

      // console.log(original_spanish.conversation[sentence_num].text)
   //    var xhr = new XMLHttpRequest()
			// var self = this
			// xhr.open('GET', apiURL + verifyResource + '?original=' + original_spanish.conversation[sentence_num].text + '&translated=' + translation);
			// xhr.onload = function() {
			// 	var result = JSON.parse(xhr.responseText)
			// 	console.log(result)
			// }
			// xhr.send()

    },

    focus: function () {
    	this.$refs.translation_input[0].focus()
    },

    getSentenceText: function (language, sequence, part) {
			return this.convos.find(function (convo) {
				return (convo.language == language && convo.sequence == sequence && convo.part == part) 
			})

		}
	}
})
