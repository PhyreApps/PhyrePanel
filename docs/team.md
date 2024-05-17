---
layout: page
sidebar: false
title: The Team
---
<script setup>
  import { VPTeamPage, VPTeamPageTitle, VPTeamPageSection, VPTeamMembers } from "vitepress/theme";
  import { teamMembers } from "./_data/team";
</script>


<style scoped>
.VPTeamPage {
  margin-bottom: 96px;
}
</style>

 <div class="VPTeamPage">
  <VPTeamPageTitle>
    <template #title>The Team</template>
    <template #lead>
        The team behind PhyrePanel is a small group of passionate developers who are dedicated to making the best open source web control panel for Linux servers.
    </template>
  </VPTeamPageTitle>
  <VPTeamPageSection>
    <template #title>Team Members</template>
    <template #members>
      <VPTeamMembers :members="teamMembers" />
    </template>
  </VPTeamPageSection>
  <!-- <VPTeamPageSection>
    <template #title>Contributors ❤️</template>
    <template #members>
      <VPTeamMembers size="small" :members="featuredContributors" />
    </template>
  </VPTeamPageSection> -->
</div>
