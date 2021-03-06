<?php


namespace Kugaudo\PhpEnum\Samples;


use Kugaudo\PhpEnum\Samples\Polymorphic\GenderPolymorphicBase;
use Kugaudo\PhpEnum\Samples\Polymorphic\GenderPolymorphicBaseExt;
use Kugaudo\PhpEnum\Samples\Polymorphic\GenderPolymorphicImpl;
use Kugaudo\PhpEnum\Samples\Polymorphic\GenderPolymorphicInterface;
use Kugaudo\PhpEnum\Samples\Simple\GenderMinimal;
use Kugaudo\PhpEnum\Samples\Simple\GenderWithHelpers;
use Kugaudo\PhpEnum\Samples\Simple\GenderWithPrimaryKey;

class UsageSample
{
    public function minimalisticSample()
    {
        // creating pointers
        $male = GenderMinimal::MALE();
        $female = GenderMinimal::FEMALE();

        // passing as method argument
        self::assert(self::greeting($male) === 'Mr.');
        self::assert(self::greeting($female) === 'Ms./Mrs.');

        // comparing pointers
        self::assert($male !== $female);
        self::assert($female === GenderMinimal::FEMALE());

        // iterating over the values with type hinting
        foreach (GenderMinimal::values() as $value) {
            self::assert(in_array($value->getTitle(), ['Male', 'Female']));
        }

        return $this;
    }

    public function primaryKeySample()
    {
        /**
         * Imitates reading from storage
         * @return GenderWithPrimaryKey
         */
        function dummyFetch() {
            // dummy calculation of referenced id
            $ref = (1 + 1) / 2;
            return GenderWithPrimaryKey::find($ref);
        }
        $gender = dummyFetch();
        self::assert($gender->getTitle() === 'Male');

        /**
         * Imitates persisting to storage
         * @param GenderWithPrimaryKey $gender
         * @return int
         */
        function dummyPersist(GenderWithPrimaryKey $gender) {
            return $gender->getPk();
        }
        $persistedRef = dummyPersist(GenderWithPrimaryKey::MALE());
        self::assert($persistedRef === 1);

        return $this;
    }

    public function helperMethodsSample()
    {
        // dynamic pointers
        $male = GenderWithHelpers::findByTitle('male');
        $female = GenderWithHelpers::findByShortCode('f');

        // calling helper methods
        self::assert($male->isMale());
        self::assert($female->isFemale());

        return $this;
    }

    public function polymorphicSample()
    {
        // the pointer of extended class is not strictly equal to base class pointer
        self::assert(GenderPolymorphicBase::MALE() !== GenderPolymorphicBaseExt::MALE());
        self::assert(GenderPolymorphicBase::FEMALE() !== GenderPolymorphicBaseExt::FEMALE());

        /// but they do hold equal data
        self::assert(GenderPolymorphicBase::MALE()->getTitle() === GenderPolymorphicBaseExt::MALE()->getTitle());
        self::assert(GenderPolymorphicBase::FEMALE()->getTitle() === GenderPolymorphicBaseExt::FEMALE()->getTitle());

        // calling base methods from child class
        foreach (GenderPolymorphicBaseExt::getHumans() as $human) {
            self::assert(in_array($human->getTitle(), ['Male', 'Female']));
        }

        // passing as method argument
        self::assert(self::polymorphicGreeting(GenderPolymorphicImpl::ALIEN()) === 'Welcome to Earth!');

        return $this;
    }

    /**
     * @param GenderMinimal $gender
     * @return string
     */
    private static function greeting(GenderMinimal $gender)
    {
        // using in switch cases
        switch ($gender) {
            case GenderMinimal::MALE():
                return 'Mr.';
            case GenderMinimal::FEMALE():
                return 'Ms./Mrs.';
        }
        throw new \LogicException('Unhandled Gender constant');
    }

    /**
     * @param GenderPolymorphicInterface $gender
     * @return string
     */
    private static function polymorphicGreeting(GenderPolymorphicInterface $gender)
    {
        if (in_array($gender->getTitle(), ['Male', 'Female'])) {
            return 'Hello!';
        } else {
            return 'Welcome to Earth!';
        }
    }

    /**
     * @param bool $assertion
     */
    private static function assert($assertion)
    {
        if (!$assertion) {
            throw new \LogicException('Something went wrong');
        }
    }
}