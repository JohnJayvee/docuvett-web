<template>
    <div class="page-container">
        <el-row :gutter="20">
            <el-col :span="6">
                <template v-for="(guideSection, menuIndex) in guideSectionArr">
                    <h4 :key="menuIndex" class="heading">
                        {{ guideSection.mainCategory }}
                    </h4>
                    <el-menu
                        :key="menuIndex"
                        default-active="0_0"
                        class="el-menu-vertical-demo"
                        @select="handleSelectMenu">
                        <el-menu-item v-for="(subCat, subCatId) in guideSection.children" :key="menuIndex + '_' + subCatId" :index="String(menuIndex + '_' + subCatId)">
                            <span>{{ subCat.heading }}</span>
                        </el-menu-item>
                    </el-menu>
                </template>
            </el-col>
            <el-col :span="18">
                <template v-for="(guideSection, menuIndex) in guideSectionArr">
                    <template v-for="(subCat, subCatId) in guideSection.children">
                        <div v-if="currentMenuIndex == String(menuIndex + '_' + subCatId)" :key="subCatId">
                            <div>
                                <h2 class="m-t-45 heading">
                                    {{ subCat.heading }}
                                </h2>
                                <p v-html="subCat.heading_desc"></p>
                            </div>

                            <template v-for="(section, sectionIndex) in subCat.children">
                                <div :key="sectionIndex">
                                    <h3 class="m-t-45 heading">
                                        {{ section.heading }}
                                    </h3>
                                    <p v-html="section.heading_desc"></p>
                                </div>
                                <el-card v-if="section.component" :key="sectionIndex" shadow="never" :class="section.componentType">
                                    <component :is="section.component" class="tab"></component>
                                </el-card>
                                <el-collapse v-if="section.codepen" :key="sectionIndex" value="0" class="code-example-collapse">
                                    <el-collapse-item title="Code Example" name="1">
                                        <iframe v-if="section.default_tab" height="265" style="width: 100%;" scrolling="no" title="Listing Page - Header - Basic Usage" :src="'https://codepen.io/kyle-vee/embed/' + section.codepen + '?height=265&theme-id=light&default-tab=' + section.default_tab" frameborder="no" loading="lazy" allowtransparency="true" allowfullscreen="true">
                                        </iframe>
                                        <iframe v-else height="265" style="width: 100%;" scrolling="no" title="Listing Page - Header - Basic Usage" :src="'https://codepen.io/kyle-vee/embed/' + section.codepen + '?height=265&theme-id=light&default-tab=html'" frameborder="no" loading="lazy" allowtransparency="true" allowfullscreen="true">
                                        </iframe>
                                    </el-collapse-item>
                                </el-collapse>
                            </template>
                        </div>
                    </template>
                </template>
            </el-col>
        </el-row>
    </div>
</template>

<style lang="scss" scoped>
    @import "~@/element-ui-colors";
    @import "~@/element-ui-variables";

    .page-container {
        max-width: 1140px;
        margin: 0 auto;

        /deep/ code {
            background-color: #f9fafc;
            padding: 0 4px;
            border: 1px solid #eaeefb;
            border-radius: 4px;
        }

        .heading {
            font-weight: 500;
        }

        /deep/ .code-example-collapse .el-collapse-item__header {

            padding: 0 10px;
        }

        /deep/ .code-example-collapse .el-collapse-item__header:hover {
            background: #f9fafc;
        }

        /deep/ .el-menu-item {
            height: 40px;
            line-height: 40px;
        }

        /deep/ .el-card.danger {
            border-color: $--color-danger;
        }

        /deep/ .el-card.success {
            border-color: $--color-success;
        }

        /deep/ .el-card .el-card__body {
            padding: 20px;
        }

        /deep/ .error-box {
            display: block;
            height: 30px;
            border: 2px solid $--color-danger;
        }
    }

    .page-container /deep/ .el-collapse-item__wrap {
        background: #fafafa;
        padding: 15px;
    }

    .page-container /deep/ .el-tabs .el-collapse-item__wrap {
        background: transparent;
    }
</style>

<script>
    import Breadcrumb from "../../views/Admin/Breadcrumb";
    import UIListingHeader from "./UI/ListingHeader";
    import UIListingHeaderBad from "./UI/ListingHeaderBad";
    import UIListingHeaderGood from "./UI/ListingHeaderGood";
    import UIListingTable from "./UI/ListingTable";
    import UIListingTableBad from "./UI/ListingTableBad";
    import UIListingTableGood from "./UI/ListingTableGood";
    import UIListingTableActions from "./UI/ListingTableActions";
    import UIDetailHeader from "./UI/DetailHeader";
    import UIDetailHeaderBad from "./UI/DetailHeaderBad";
    import UIDetailHeaderGood from "./UI/DetailHeaderGood";
    import UIDetailContent from "./UI/DetailContent";
    import DetailContentFirstCol from "./UI/DetailContentFirstCol";
    import UIDetailContentBad from "./UI/DetailContentBad";
    import UIDetailContentGood from "./UI/DetailContentGood";
    import UIDetailContentButtons from "./UI/DetailContentButtons";
    import UIDetailContentButtonsBad from "./UI/DetailContentButtonsBad";
    import UIDetailContentButtonsGood from "./UI/DetailContentButtonsGood";
    import UIDetailDialogStructure from "./UI/DialogStructure";
    import UIDetailDialogStructureBad from "./UI/DialogStructureBad";
    import UIDetailDialogStructureGood from "./UI/DialogStructureGood";
    import UIResponsiveness from "./UI/Responsiveness";
    import UIResponsivenessDialog from "./UI/ResponsivenessDialog";
    import UIResponsivenessDialogMin from "./UI/ResponsivenessDialogMin";
    import UIButtonState from "./UI/ButtonState";
    import UIButtonAdd from "./UI/ButtonAdd";
    import UIButtonsDialog from "./UI/ButtonsDialog";
    import UIPageContent from "./UI/PageContent";
    import UIPlaceholder from "./UI/Placeholder";
    import UIInputLabel from "./UI/InputLabel";
    import UIDynamicTable from "./UI/DynamicTable";
    import UICollapse from "./UI/Collapse";
    import UIGallery from "./UI/Gallery";

    export default {
        name: "UIStyleGuide",
        components:{
            Breadcrumb,
            UIListingHeader,
            UIListingHeaderBad,
            UIListingHeaderGood,
            UIListingTable,
            UIDetailHeader,
            DetailContentFirstCol,
            UIDetailContentButtons,
            UIDetailContentButtonsBad,
            UIDetailContentButtonsGood,
            UIDetailDialogStructure,
            UIDetailDialogStructureBad,
            UIDetailDialogStructureGood,
            UIResponsiveness,
            UIResponsivenessDialog,
            UIResponsivenessDialogMin,
            UIButtonState,
            UIButtonAdd,
            UIButtonsDialog,
            UIPageContent,
            UIPlaceholder,
            UIInputLabel,
            UIDynamicTable,
            UICollapse,
            UIGallery
        },
        props: [],
        data() {
            return {
                currentMenuIndex: '0_0',
                filters: {
                    search: ''
                },
                guideSectionArr: [
                    {
                        mainCategory: 'General',
                        children: [
                            {
                                heading: 'Buttons',
                                heading_desc: 'Using consistent styling and colours for different purposes of a button help guide the user to the most relevant button for their needs even if they might not be 100% sure.',
                                children: [
                                    {
                                        heading: 'Button state',
                                        heading_desc: 'Check all button states, active, hover and disabled to make sure styling is correct.',
                                        component: UIButtonState,
                                    },
                                    {
                                        heading: 'Add button',
                                        heading_desc: 'This button should typically be in the page header and should be set to the primary colour.',
                                        component: UIButtonAdd,
                                    },
                                    {
                                        heading: 'Dialog buttons',
                                        heading_desc: 'The dialog footer should always contain a Save or Update button <code>success</code> and a cancel button <code>plain</code>',
                                        component: UIButtonsDialog,
                                    },
                                ]
                            },
                            {
                                heading: 'Page Content',
                                heading_desc: 'The main page content should not be wrapped in <code>el-card</code>.',
                                children: [
                                    {
                                        heading: 'Example',
                                        heading_desc: 'All input fields should be size <code>small</code>',
                                        component: UIPageContent,
                                    },
                                ]
                            },
                            {
                                heading: 'Placeholders',
                                heading_desc: 'Be descriptive with the placeholder, "select" is not clear enough.',
                                children: [
                                    {
                                        heading: 'Example',
                                        heading_desc: '',
                                        component: UIPlaceholder,
                                    },
                                ]
                            },
                            {
                                heading: 'Input Labels',
                                heading_desc: 'Default form label position should be inline, with text aligned right. In scenario\'s where the form is in a narrow container, you can use label position top.',
                                children: [
                                    {
                                        heading: 'Example',
                                        heading_desc: '',
                                        component: UIInputLabel,
                                    },
                                ]
                            },
                            {
                                heading: 'Dynamic Table',
                                heading_desc: 'If a table or list of data needs to be dynamic where the user can add or delete line items.',
                                children: [
                                    {
                                        heading: 'Example',
                                        heading_desc: '',
                                        component: UIDynamicTable,
                                    },
                                ]
                            },
                            {
                                heading: 'Collapse',
                                heading_desc: 'If a page or form needs to hold a large amount of data or fields then it is best to seperate the content into smaller sections using <code>el-collapse</code>',
                                children: [
                                    {
                                        heading: 'Example',
                                        heading_desc: '',
                                        component: UICollapse,
                                    },
                                ]
                            },
                            {
                                heading: 'Gallery',
                                heading_desc: 'Collection of screenshots and descriptions to give a general idea of things to avoid doing.',
                                children: [
                                    {
                                        heading: '',
                                        heading_desc: '',
                                        component: UIGallery,
                                    },
                                ]
                            },
                        ]
                    },{
                        mainCategory: 'Listing Data',
                        children: [
                            {
                                heading: 'Header',
                                heading_desc: 'The header of a listing page.',
                                children: [
                                    {
                                        heading: 'Basic usage',
                                        heading_desc: 'The header contains <code>Page Title</code>, <code>Breadcrumbs</code>, <code>Add Button</code> and <code>Search Bar</code>',
                                        component: UIListingHeader,
                                        codepen: 'dypbWLa'
                                    },
                                    {
                                        heading: 'Bad Example',
                                        heading_desc: 'Missing the <code>Add</code> button and the <code>Search</code> input field.',
                                        component: UIListingHeaderBad,
                                        componentType: 'danger',
                                        codepen: 'LYRJxBO'
                                    },
                                    {
                                        heading: 'Good Example',
                                        heading_desc: '<code>Add</code> button positioned to the left.  <code>Search</code> input field positioned to the right.',
                                        component: UIListingHeaderGood,
                                        componentType: 'success',
                                        codepen: 'eYdLgLz'
                                    },
                                    {
                                        heading: 'Page Title',
                                        heading_desc: 'Page titles use the <code>h2</code> tag with <code>class="page-title"</code>',
                                    },
                                    {
                                        heading: 'Breadcrumbs',
                                        heading_desc: 'The Breadcrumbs component will render based on the meta information given in <code>routes.js</code>',
                                        codepen: 'jOMNgyv',
                                        default_tab: 'js'
                                    },
                                    {
                                        heading: 'Add Button',
                                        heading_desc: '<p>An Icon Button that will open a Dialog. Always displayed in the bottom <strong>LEFT</strong> corner of the header.</p><p>Attributes: <code>type="primary"</code> and <code>icon="el-icon-plus"</code></p>',
                                    },
                                    {
                                        heading: 'Search Bar',
                                        heading_desc: '<p>Used to filter the data displayed in the page table. Always displayed in the bottom <strong>RIGHT</strong> corner of the header.</p><p>Attributes: <code>placeholder="Search..."</code></p><p>Events: <code>@input="applySearch"</code>. This function should have a debounce value of <code>300</code> to avoid spamming requests.</p>',
                                        codepen: 'OJRJLJB'
                                    }
                                ]
                            },
                            {
                                heading: 'Table',
                                heading_desc: 'Displays the main data on the page.',
                                children: [
                                    {
                                        heading: 'Basic usage',
                                        heading_desc: '<p>The table should always have <code>Column Sorting</code> and <code>Pagination</code>.</p><p>The top filter row is optional, dropdown filters positioned to the <strong>left</strong>, date filters positioned to the <strong>right</strong>.</p><p>The last column of the table should always be <code>Actions</code>, which will contain buttons to manage the data.</p>',
                                        component: UIListingTable,
                                        codepen: 'NWRWYej'
                                    },
                                    {
                                        heading: 'Bad Example',
                                        heading_desc: '<code>Actions</code> column is not positioned last. Buttons are not ordered by <code>type</code>',
                                        component: UIListingTableBad,
                                        componentType: 'danger',
                                        codepen: 'LYRJxge'
                                    },
                                    {
                                        heading: 'Good Example',
                                        heading_desc: '<code>Edit</code> & <code>Delete</code> buttons should always be present. The rest can be added as needed.',
                                        component: UIListingTableGood,
                                        componentType: 'success',
                                        codepen: 'OJRoWBq'
                                    },
                                    {
                                        heading: 'Actions',
                                        heading_desc: '<p><code>View / Edit</code> button must appear first, with attribute type: <code>plain</code></p><p><code>Delete</code> button must appear last, with attribute type: <code>danger</code>.</p><p>Any further required buttons will fit in between, the order in which it is displayed should be based on the <code>type</code> of the button.Type order: <code>primary</code> <code>success</code> <code>info</code> <code>warning</code></p><p>Buttons with type <code>warning</code> or <code>danger</code> need a confirmation dialog to proceed.</p>',
                                        component: UIListingTableActions,
                                        codepen: 'RwGwMvP'
                                    },
                                ]
                            },
                        ]
                    },
                    {
                        mainCategory: 'Data Detail Page',
                        children: [
                            {
                                heading: 'Header',
                                heading_desc: 'The header of a details page.',
                                children: [
                                    {
                                        heading: 'Basic usage',
                                        heading_desc: 'The header contains the <code>Singular Form Title</code> of the model and the <code>ID</code>. Followed by <code>Breadcrumbs</code>',
                                        component: UIDetailHeader,
                                        codepen: 'jOMvBOW'
                                    },
                                    {
                                        heading: 'Bad Example',
                                        heading_desc: 'Missing the <code>ID</code> element and title is not in singular form.',
                                        component: UIDetailHeaderBad,
                                        componentType: 'danger',
                                        codepen: 'VwKGpwG'
                                    },
                                    {
                                        heading: 'Good Example',
                                        heading_desc: '<code>Add</code> button positioned to the left.  <code>Search</code> input field positioned to the right.',
                                        component: UIDetailHeaderGood,
                                        componentType: 'success',
                                        codepen: 'QWKVpwj'
                                    },
                                ]
                            },
                            {
                                heading: 'Content',
                                heading_desc: '<p>The main content area is divided into 2 columns, <code>:span="6"</code> and <code>:span="18"</code> respectively.</p><p>The first column is dedicated to basic details about the model, with the second column dedicated to the rest of the content needed.</p>',
                                children: [
                                    {
                                        heading: 'Basic usage',
                                        heading_desc: '<p>Content is firstly placed in <code>Tabs</code> to organise content horizontally. Within <code>Tabs</code> content can be organised vertically using <code>Collapse</code></p>',
                                        component: UIDetailContent,
                                        codepen: 'oNzPZgy'
                                    },
                                    {
                                        heading: 'First Column - Basic details',
                                        heading_desc: '<p>Information that is quick and easy to access while performing other operations.</p><p>The form should be editable, with the option to delete the model.</p>',
                                        component: DetailContentFirstCol,
                                        codepen: 'yLaxMNa'
                                    },
                                    {
                                        heading: 'Bad Example',
                                        heading_desc: 'Content is not placed in a <code>Tab</code>. Form label position is wrong, it should be position right.',
                                        component: UIDetailContentBad,
                                        componentType: 'danger',
                                        codepen: 'xxEaqGY'
                                    },
                                    {
                                        heading: 'Good Example',
                                        heading_desc: 'Content is placed correctly in a <code>Tab</code> with the form organised into a <code>Collapse Item</code>.',
                                        component: UIDetailContentGood,
                                        componentType: 'success',
                                        codepen: 'RwGYpPv'
                                    },
                                ]
                            },
                            {
                                heading: 'Action Buttons',
                                heading_desc: '<p>Buttons can either be placed before or after content.</p> All buttons related to CRUD operations are to be placed <strong>AFTER</strong> the content. Any other buttons needed are to be placed before content.',
                                children: [
                                    {
                                        heading: 'Basic usage',
                                        heading_desc: '',
                                        component: UIDetailContentButtons,
                                        codepen: 'zYKJZvE'
                                    },
                                    {
                                        heading: 'Bad Example',
                                        heading_desc: '',
                                        component: UIDetailContentButtonsBad,
                                        componentType: 'danger',
                                        codepen: 'LYRJWpr'
                                    },
                                    {
                                        heading: 'Good Example',
                                        heading_desc: '',
                                        component: UIDetailContentButtonsGood,
                                        componentType: 'success',
                                        codepen: 'GRjXWpL'
                                    },
                                ]
                            },
                        ]
                    },
                    {
                        mainCategory: 'Dialogs',
                        children: [
                            {
                                heading: 'Overview',
                                heading_desc: 'Dialogs are commonly used for add / edit forms but can be used for various reasons.',
                                children: [
                                    {
                                        heading: 'Structure',
                                        heading_desc: '<p>The dialog window has three main sections, <code>Title</code>, <code>Body</code> and the <code>Footer</code> which will contain any buttons needed.</p><p>If a large amount of data needs to be displayed use a 2 column layout so the columns can be stacked on mobiles.</p>',
                                        component: UIDetailDialogStructure,
                                        codepen: 'ZEpMeQe'
                                    },
                                    {
                                        heading: 'Bad Example',
                                        heading_desc: 'Missing the <code>Title</code> and <code>Footer</code> with wrong sizing.',
                                        component: UIDetailDialogStructureBad,
                                        componentType: 'danger',
                                        codepen: 'LYRJWGr'
                                    },
                                    {
                                        heading: 'Good Example',
                                        heading_desc: 'An example of dialog with a lot of data.',
                                        component: UIDetailDialogStructureGood,
                                        componentType: 'success',
                                        codepen: 'gOwdmPJ'
                                    },
                                ]
                            },
                        ]
                    },
                    {
                        mainCategory: 'Responsiveness',
                        children: [
                            {
                                heading: 'Grid Layout',
                                heading_desc: 'Most users will access the app using a computer however we need to aim to make it as user friendly as possible on a tablet device as well.',
                                children: [
                                    {
                                        heading: 'Basic Usage',
                                        heading_desc: '<p>Utilize ElementUI basic grid layout to ensure easy control on elements sizes at various breakpoints. The main breakpoints to consider is <code>sm</code> for tablets, <code>lg</code> for laptops or desktops.</p><p>Rows should have the attribute <code>:gutter="20"</code></p>',
                                        component: UIResponsiveness,
                                        codepen: 'zYKJZqP'
                                    },
                                ]
                            },
                            {
                                heading: 'Dialogs',
                                heading_desc: 'Dialogs need to be responsive in both their window size and the content they hold.',
                                children: [
                                    {
                                        heading: 'Height',
                                        heading_desc: '<p>The dialog window can be a maximum height of 80vh, content should then become scrollable within the dialog window.</p>',
                                        component: UIResponsivenessDialog,
                                        codepen: 'yLaxMmv'
                                    },
                                    {
                                        heading: 'Width',
                                        heading_desc: '<p><strong>For tablets:</strong> <code>600px</code> width. <p><strong>For Desktop:</strong> <code>600px</code> min width to <code>80vw</code> max width.',
                                        component: UIResponsivenessDialogMin,
                                        codepen: 'qBaMrew'
                                    },
                                ]
                            },
                        ]
                    }
                ]
            };
        },
        mounted() {
        },
        methods: {
            handleSelectMenu(index) {
                this.currentMenuIndex = index;
            },
        }
    };
</script>

