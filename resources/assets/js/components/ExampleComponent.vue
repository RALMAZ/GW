<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        I'm an example component.
                            <el-tabs v-model="activeName" @tab-click="handleClick">
                              <el-tab-pane label="User" name="first">User</el-tab-pane>
                              <el-tab-pane label="Config" name="second">Config</el-tab-pane>
                              <el-tab-pane label="Role" name="third">Role</el-tab-pane>
                              <el-tab-pane label="Task" name="fourth">Task</el-tab-pane>
                            </el-tabs>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
          return {
            activeName: 'first'
          };
        },
        methods: {
          handleClick(tab, event) {
            console.log(tab, event);
          }
        }
    }
</script>
