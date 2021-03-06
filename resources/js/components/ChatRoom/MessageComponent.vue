<template>
    <div class="chat_container">
        <div class="chat_header">
            <span>{{ user_email }}</span>
            <a v-if="isMobile()" href="#" class="chat_button" data-tip="Chatroom" onclick="chatroom_toggle(event, $(this))"></a>
            <a v-if="!isMobile()" href="#" class="popup" data-tip="Popup" onclick="popupChat(event, $(this))"></a>
        </div>
        <vue-custom-scrollbar class="chat_text_container" id="chat_text_container">
            <div v-if="ignored_users.length > 0" class="ignoredUsers" v-for="(user,key) in ignored_users" :key="key">
                <p >{{user.user_name}} #{{user.user_id}} {{user.timestamp}} IGNORED <span class="show" @click="showUser(user.user_id)">Show</span></p>
            </div>
            <div v-if="isMessages">
                <div v-for="(message,index) in messages" :key="`message-${index}`" v-if="!checkIgnore(message.user_id)" class="user_msg" :class="'user-' + message.user_id" >
                  <p class="user_info">
                      <span :class="'flag-icon flag-icon-' + message.country_code"></span>
                      <img class="margin-left-5" :src="'/images/emoticons/smiles/'+message.race" alt="">
                      <span class="username" @click="selectUser(`${message.user_id}`)">{{message.user_name}}</span>
                      <span class="user_id"><a :href="'/user/' + message.user_id" >#{{message.user_id}}</a></span>
                      <span class="ignore_user" v-if="userId!=message.user_id" @click="ignoreUser(message)">Ignore</span>
                      <span class="msg_timestamp">{{convertTo(message.created_at)}}</span>
                  </p>
                  <p class="msg_text">
                    <span  :class="setClass(message.to)" v-html="showMessage(index)"></span>
                    <span class="more_less" v-if="message.more_length && message.show_more" @click="show_more(index)"> More</span>
                    <span class="more_less" v-if="message.more_length && !message.show_more" @click="show_less(index)"> Less</span>
                  </p>
                </div>
            </div>
             <div v-if="!isMessages">
                 Empty messages
             </div>

        </vue-custom-scrollbar>
        <div class="chat_footer" v-if="userLoggedin">
            <div class="send" style="position: relative">
              <SmileComponent :status="chat_action.smile" @turnOffStatus="turnOffStatus"></SmileComponent>
              <ImageComponent :status="chat_action.image" @turnOffStatus="turnOffStatus"></ImageComponent>
              <FSizeComponent :status="chat_action.size" @turnOffStatus="turnOffStatus"></FSizeComponent>
              <FColorComponent :status="chat_action.color" @turnOffStatus="turnOffStatus"></FColorComponent>

              <div class="extra">
                <p class="bold" @click="bold()"></p>
                <p class="italic" @click="italic()"></p>
                <p class="underline" @click="underline()"></p>
                <!--  -->
                <p class="link" @click="link()"></p>
                <p class="img" @click="img()"></p>
                <!--  -->
                <p class="font_size" @click="selectItem('size')"></p>
                <p class="font_color" @click="selectItem('color')"></p>
                <!--  -->
                <p class="pic" @click="selectItem('image')"></p>
                <p class="smile" @click="selectItem('smile')"></p>
                <p class="meme" @click="meme()">[d]</p>
              </div>
              <div class="input-group">
                <textarea-autosize
                  v-model="message"
                  @keyup.enter.exact.native="sendMessage($event)"
                  @keydown.enter.ctrl.exact.native="newline"
                  @keydown.enter.shift.exact.native="newline"
                  placeholder="Введите сообщение и нажмите Enter"
                  :min-height="49"
                  :max-height="350"
                  :autosize="false"
                  class="form-control"
                  id="editor"
                  ref="input"
                ></textarea-autosize>
              </div>
            </div>
        </div>
        <div class="chat_footer" v-if="!userLoggedin">
          <p class='guests_message'> Please login to chat!</p>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

import * as chatHelper from '../../helper/chatHelper';
import * as utilsHelper from '../../helper/utilsHelper';

import vueCustomScrollbar from 'vue-custom-scrollbar';

import FColorComponent from './FontColorComponent.vue';
import FSizeComponent from './FontSizeComponent.vue';
import ImageComponent from './ImageComponent.vue';
import SmileComponent from './SmileComponent.vue';

export default {
  components: {
    vueCustomScrollbar,FColorComponent, FSizeComponent, ImageComponent, SmileComponent
  },
  props: {
    auth: [Object, Number],
    socket: [Object],
    messages: [Array],
    isMessages: [Boolean]
  },
  data() {
    return {
      settings: {
        maxScrollbarLength: 60
      },

      userLoggedin: false,
      message: "",
      user: this.auth,
      ignored_userIDs: [],
      ignored_users: [],
      chat_action : {
        'smile': false,
        'image': false,
        'color': false,
        'size': false,
      }
    };
  },
  computed: {
    user_email: function() {
      if (this.auth != 0) {
        this.userLoggedin = true;
        return this.auth.email;
      } else {
        return "Guest";
      }
    },
    userId: function() {
      if (this.auth != 0) {
        return this.auth.id;
      } else {
        return 0;
      }
    }
  },

  methods: {
    setClass: function(user_id) {
      if(user_id == this.user.id) {
        return 'highlight';
      }
    },

    newline() {
      this.message = `${this.message}\r\n`;
    },

    selectUser(user) {
      chatHelper.insertText('@' + user + ',');
    },

    sendMessage(event) {
      if (this.message.length > 0) {
        let message = utilsHelper.wrapperTxt(this.message);

        let checkmessage = utilsHelper.pregMatchFunction(message);
        if (checkmessage.trim().length == 0) {
          this.message = '';
          alert("Please input text.");
          return false;
        }
        message = utilsHelper.ValidImgUrl(message);
        let messagePacket = this.createMsgObj(message);
        let currentObj = this;
        event.preventDefault();
        let self = this;
        axios.post('/chat/insert_message', messagePacket)
        .then(function (response) {
            if(response.data.status == 'ok') {
              let data = {'id':response.data.id, 'user_id': response.data.user}
              self.socket.emit("sendMessage", data);
            }
        })
        .catch(function (error) {
            currentObj.output = error;
        });

        this.message = "";
      } else {
        alert("Please Enter Your Message.");
      }
    },

    createMsgObj: function(message) {
      return {
        user_id: this.auth.id,
        file_path: "",
        message: message,
        imo: ""
      };
    },

    convertTo: utilsHelper.convertTo,
    isMobile: utilsHelper.isMobile,

    ignoreUser: function(userMsg) {
      this.ignored_userIDs.push(userMsg.user_id);
      let ignoreUser = {'user_id': userMsg.user_id, 'user_name': userMsg.user_name, 'timestamp': utilsHelper.convertTo(new Date)}
      this.ignored_users.push(ignoreUser);
    },

    checkIgnore: function(user_id) {
      return this.ignored_userIDs.indexOf(user_id) == -1 ? false : true
    },

    showUser: function(user_id) {
      this.ignored_userIDs.splice(this.ignored_userIDs.indexOf(user_id), 1);

      let index = this.ignored_users.findIndex(
        element => element.user_id === user_id
      );
      this.ignored_users.splice(index, 1);
    },

    popupChat: function() {
      this.$emit("onPopup", {
        visibleFormCrud: true
      });
    },

    getSelection: chatHelper.getSelection,
    bold: chatHelper.bold,
    italic: chatHelper.italic,
    underline: chatHelper.underline,
    link: chatHelper.link,
    img: chatHelper.img,
    meme: chatHelper.meme,

    selectItem: function(type) {
      let self = this;
      Object.keys(self.chat_action).forEach(function(key) {
        if(type === key) self.chat_action[key] = !self.chat_action[key]
        else self.chat_action[key] = false;
      })
    },

    turnOffStatus: function() {
      let self = this;
      Object.keys(self.chat_action).forEach(function(key) {
        self.chat_action[key] = false;
      })
    },

    showMessage: function(index) {
      return this.messages[index].show_more ? this.messages[index].short_msg : this.messages[index].message;
    },

    show_more: function(index) {
      this.messages[index].show_more = false;
    },

    show_less: function(index) {
      this.messages[index].show_more = true;
    },

  }
};
</script>
