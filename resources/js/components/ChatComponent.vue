<template>
  <div class="row">
    <div class="col-md-8">
      <div class="card card-default">
        <div class="card-header">Messages</div>
        <div class="card-body p-0">
          <ul
            class="list-unstyled"
            style="height: 300px; overflow-y: scroll"
            v-chat-scroll
          >
            <li class="p-2" v-for="(message, index) in messages" :key="index">
              <strong>{{ message.user.name }}</strong>
              {{ message.message }}
            </li>
          </ul>
        </div>
        <input
          @keyup.enter="sendMessage"
          @keydown="typingEvent"
          v-model="newMessage"
          type="text"
          class="form-control"
          name="message"
          placeholder="Enter Your Message"
        />
      </div>
      <span class="text-muted" v-if="activeUser"
        >{{ activeUser.name }} is typing...</span
      >
    </div>

    <div class="col-md-4">
      <div class="card card-default">
        <div class="card-header">Active Users</div>
        <div class="card-body">
          <ul>
            <li class="p-2" v-for="(user, index) in users" :key="index">
              {{ user.name }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user"],
  mounted() {},
  data() {
    return {
      messages: [],
      newMessage: "",
      users: [],
      activeUser: false,
      typingTimer: false,
    };
  },

  created() {
    this.fetchMessages();
    Echo.join("chats-channel")
      .here((user) => {
        this.users = user;
      })
      .joining((user) => {
        this.users.push(user);
      })
      .leaving((user) => {
        this.users = this.users.filter((u) => {
          return u.id != user.id;
        });
      })
      .listenForWhisper("typing", (response) => {
        this.activeUser = response;
        if (this.typingTimer) {
          clearTimeout(this.typingTimer);
        }

        this.typingTimer = setTimeout(() => {
          this.activeUser = false;
        }, 1000);
      })
      .listen("MessageSent", (event) => {
        this.messages.push(event.message);
      });
  },

  methods: {
    sendMessage() {
      this.messages.push({ message: this.newMessage, user: this.user });
      axios.post("messages", { message: this.newMessage });
      this.newMessage = "";
    },
    fetchMessages() {
      axios.get("messages").then((response) => {
        this.messages = response.data;
      });
    },
    typingEvent() {
      Echo.join("chats-channel").whisper("typing", this.user);
    },
  },
};
</script>
